<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\models\registry\RLog;
use app\models\reference\SResponseEquipment;
use app\models\reference\SResponseInformationSystem;
use app\models\reference\SResponseWarrantySupport;
use app\models\reference\SOrganization;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 * @property RLog[] $rLogs
 * @property SResponseEquipment[] $sResponseEquipments
 * @property SResponseInformationSystem[] $sResponseInformationSystems
 * @property SResponseWarrantySupport[] $sResponseWarrantySupports
 * @property SOrganization $organization0
 * @property User $leader0
 * @property User[] $users
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /*
        После сохранения
    */
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        // логируем изменение
        if (!$insert) {
            $forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
            $forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
            $datathisuser = $this->last_name.' '.$this->first_name.' ('.$this->organization0->name.')';
            if ($this->username)  // если задан логин, то это Пользователь системы
                Yii::$app->systemlog->dolog(Yii::$app->user->id, 504, "Пользователь {$forLogUserName} изменил информацию о системном пользователе: {$datathisuser}");
            else // иначе это простое ответственное лицо
                Yii::$app->systemlog->dolog(Yii::$app->user->id, 502, "Пользователь {$forLogUserName} изменил информацию об ответственном лице: {$datathisuser}");
        }
    }

    /*
        После удаления
    */
    public function afterDelete()
    {
        parent::afterDelete();
        // логируем удаление
        $forLogUserModel = \app\models\User::findOne(Yii::$app->user->id);
        $forLogUserName = $forLogUserModel->last_name.' '.$forLogUserModel->first_name;
        $datathisuser = $this->last_name.' '.$this->first_name.' ('.$this->organization0->name.')';
        if ($this->username)  // если задан логин, то это Пользователь системы
            Yii::$app->systemlog->dolog(Yii::$app->user->id, 503, "Пользователь {$forLogUserName} удалил системного пользователя: {$datathisuser}");
        else // иначе это простое ответственное лицо
            Yii::$app->systemlog->dolog(Yii::$app->user->id, 501, "Пользователь {$forLogUserName} удалил ответственное лицо: {$datathisuser}");
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['first_name', 'last_name', 'organization'], 'required'],
            [['created_at', 'updated_at', 'organization', 'leader'], 'integer'],
            [['username','password_hash','password_reset_token','email','position','additional'], 'string', 'max' => 255],
            [['first_name', 'last_name', 'second_name', 'phone_work', 'phone_cell', 'skype'], 'string', 'max' => 128],
            [['auth_key'], 'string', 'max' => 32],
            [['email'], 'email'],
            ['password_hash', '__setValidatePassword'],
        ];
    }

    public $backup_hash;

    public function __setValidatePassword($attribute)
    {
        if ( $this->$attribute !== 'password') // 'password' недопустимый пароль, случжит для сохранения старого пароля
            $this->setPassword($this->$attribute);
        elseif ( !is_null($this->backup_hash) )
            $this->$attribute = $this->backup_hash;
        else
            $this->$attribute = '';
    }

    public function load( $data, $formName = null ) // override
    {
        $this->backup_hash = $this->password_hash;
        return parent::load($data, $formName);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'second_name' => 'Отчество',
            'organization' => 'Организация',
            'position' => 'Должность',
            'phone_work' => 'Рабочий телефон',
            'phone_cell' => 'Сотовый телефон',
            'additional' => 'Доп.способ связи',
            'leader' => 'Руководитель',
            'password_hash' => 'Пароль'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if ($this->password_hash) // если пароль у пользователя не указан, то пока просто отваливаемся
            return Yii::$app->security->validatePassword($password, $this->password_hash);
        else return false;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRLogs()
    {
        return $this->hasMany(RLog::className(), ['user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSResponseEquipments()
    {
        return $this->hasMany(SResponseEquipment::className(), ['response_person' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSResponseInformationSystems()
    {
        return $this->hasMany(SResponseInformationSystem::className(), ['response_person' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSResponseWarrantySupports()
    {
        return $this->hasMany(SResponseWarrantySupport::className(), ['response_person' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization0()
    {
        return $this->hasOne(SOrganization::className(), ['id' => 'organization']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeader0()
    {
        return $this->hasOne(User::className(), ['id' => 'leader']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['leader' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rLogs
                || $this->sResponseEquipments
                    || $this->sResponseInformationSystems
                        || $this->sResponseWarrantySupports
                            || $this->users )
            return false;
        $this->delete();
        return true;
    }

    /*
        Проверка возможных связей
    */
    public function checkConstraint(){
        if ( $this->rLogs
                || $this->sResponseEquipments
                    || $this->sResponseInformationSystems
                        || $this->sResponseWarrantySupports
                            || $this->users )
            return true;
        return false;
    }

    /*
        Возвращает список имен всех Auth_Item, куда входит данный пользователь рекурсивно
    */
    public static function getThisUserGroupsRecursive($user_id)
    {
        return \Yii::$app->db->createCommand(
            'SELECT aic2.child
               FROM auth_item_child aic1, auth_item_child aic2
              WHERE aic1.parent in (
                        SELECT item_name
                          FROM auth_assignment
                         WHERE user_id = :id
                    )
                AND aic1.child = aic2.parent
              UNION
             SELECT child
               FROM auth_item_child
              WHERE parent in (
                        SELECT item_name
                          FROM auth_assignment
                         WHERE user_id = :id
                    )
              UNION
             SELECT item_name
               FROM auth_assignment
              WHERE user_id = :id'
        )
        ->bindValue(':id', $user_id)
        ->queryColumn();
    }

    /*
        Возвращает ФИО пользователя в формате Фамилия Имя Отчество
    */
    public function getFullName()
    {
        return $this->last_name.' '.$this->first_name.' '.$this->second_name;
    }


    /*
     * Для АПИ
     * */

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['username'], $fields['auth_key'], $fields['password_hash'],
            $fields['password_reset_token'], $fields['status'], $fields['created_at'],
            $fields['updated_at'],$fields['is_system_user'],
            $fields['additional']);
        return $fields;
    }
}
