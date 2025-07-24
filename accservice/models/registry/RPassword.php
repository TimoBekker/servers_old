<?php

namespace app\models\registry;

use Yii;
use app\components\utils\Cryptonite;

/**
 * This is the model class for table "r_password".
 *
 * @property string $id
 * @property string $login
 * @property string $password
 * @property string $description
 * @property string $equipment
 * @property string $information_system
 * @property string $next
 *
 * @property REquipment $equipment0
 * @property RInformationSystem $informationSystem
 * @property RPassword $next0
 * @property RPassword[] $rPasswords
 */
class RPassword extends \yii\db\ActiveRecord
{
    const FAKE_PASS = '*******';

    public $confirmation;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_password';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            [['equipment', 'information_system', 'next'], 'integer'],
            [['login', 'password', 'description'], 'string', 'max' => 255],
            ['confirmation', 'required'],
            ['password', 'confirmPassword'],
        ];
    }

    public function confirmPassword($attribute)
    {
        if ( strcmp($this->$attribute, $this->confirmation ) !== 0) {
            $this->addError( $attribute, 'Подтверждение пароля не совпадает' );
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'password' => 'Пароль',
            'description' => 'Описание',
            'confirmation' => 'Подтверждение',
            'equipment' => 'Equipment',
            'information_system' => 'Information System',
            'next' => 'Next',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInformationSystem()
    {
        return $this->hasOne(RInformationSystem::className(), ['id' => 'information_system']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNext0()
    {
        return $this->hasOne(RPassword::className(), ['id' => 'next']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRPasswords()
    {
        return $this->hasMany(RPassword::className(), ['next' => 'id']);
    }


    public function beforeSave($insert){
        if (parent::beforeSave($insert)) {
            if ( $this->password ){
                // var_dump(Cryptonite::encodePassword($this->password));exit;
                $this->password = Cryptonite::encodePassword($this->password);
            }
            return true;
        } else {
            return false;
        }
    }

}
