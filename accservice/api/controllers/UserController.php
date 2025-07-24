<?php
/**
 * Created by PhpStorm.
 * User: custom
 * Date: 29.08.18
 * Time: 17:35
 */

namespace app\api\controllers;


use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = User::class;
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
            'authenticator' => [
                'class' => HttpHeaderAuth::className(),
            ],
            'rateLimiter' => [
                'class' => RateLimiter::className(),
            ],
        ];
    }
}