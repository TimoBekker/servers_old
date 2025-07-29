<?php

namespace app\components;

use Yii;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    public static function generateToken($userId)
    {
        $key = Yii::$app->params['jwtSecretKey'];
        $payload = [
            'iss' => 'accservice',
            'aud' => 'accservice',
            'iat' => time(),
            'exp' => time() + Yii::$app->params['jwtExpire'],
            'uid' => $userId,
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function validateToken($token)
    {
        try {
            $key = Yii::$app->params['jwtSecretKey'];
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
}