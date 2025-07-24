<?php
/**
 * Created by PhpStorm.
 * User: custom
 * Date: 29.08.18
 * Time: 17:52
 */

namespace app\api\controllers;


use app\models\reference\SOrganization;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class OrganizationController extends ActiveController
{
    public $modelClass = SOrganization::class;
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