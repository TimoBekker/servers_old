<?php
/**
 * Created by PhpStorm.
 * User: custom
 * Date: 30.08.18
 * Time: 17:35
 */

namespace app\api\controllers;


use app\models\relation\NnEquipmentInfosysContour;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class EinfosystemsController extends ActiveController
{
    public $modelClass = NnEquipmentInfosysContour::class;
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index', 'view', 'create', 'update'],
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