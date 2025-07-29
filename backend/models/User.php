<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\components\JwtHelper;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $email
 * @property string $first_name
 * @property string $last_name
 * @property string $second_name
 * @property integer $organization
 * @property string $position
 * @property string $phone_work
 * @property string $phone_cell
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        $decoded = JwtHelper::validateToken($token);
        if (!$decoded) {
            return null;
        }

        return static::findOne(['id' => $decoded->uid, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function validatePassword($password)
    {
        if ($this->password_hash) {
            return Yii::$app->security->validatePassword($password, $this->password_hash);
        }
        return false;
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function getOrganization0()
    {
        return $this->hasOne(\app\models\reference\SOrganization::class, ['id' => 'organization']);
    }

    public function getFullName()
    {
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->second_name;
    }
}