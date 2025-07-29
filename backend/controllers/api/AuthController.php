<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
use app\models\User;
use app\components\JwtHelper;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:3000'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['login', 'options'],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        if (!$username || !$password) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Необходимо указать имя пользователя и пароль'];
        }

        $user = User::findByUsername($username);
        if (!$user || !$user->validatePassword($password)) {
            Yii::$app->response->statusCode = 401;
            return ['error' => 'Неверное имя пользователя или пароль'];
        }

        $token = JwtHelper::generateToken($user->id);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ]
        ];
    }

    public function actionLogout()
    {
        return ['message' => 'Успешный выход'];
    }

    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        
        return [
            'id' => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'organization' => $user->organization0 ? $user->organization0->name : null,
        ];
    }

    public function actionOptions()
    {
        return [];
    }
}