<?php
/**
 * Created by PhpStorm.
 * User: custom
 * Date: 30.08.18
 * Time: 11:33
 */

namespace app\api\controllers;


use app\models\reference\CVolumeHdd;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class HarddrivesController extends ActiveController
{
    public $modelClass = CVolumeHdd::class;

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index', 'view', 'create', 'update'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                    // 'application/xml' => \yii\web\Response::FORMAT_XML,
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