<?php
namespace app\api\models;

use yii\base\Security;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Created by PhpStorm.
 * User: custom
 * Date: 30.08.18
 * Time: 14:19
 */

class Extern extends ActiveRecord implements IdentityInterface
{
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        throw new NotSupportedException('"getAuthKey" не реализован.');
    }

    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('"validateAuthKey" не реализован.');
    }

    public function regenerateToken() {
        $security = new Security();
        $this->token = $security->generateRandomString();
    }
}