<?php
/**
 * Created by PhpStorm.
 * User: custom
 * Date: 30.08.18
 * Time: 10:22
 */

namespace app\api\controllers;

use app\models\reference\CVlanNet;
use app\models\relation\NnEquipmentVlan;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class IpaddressController extends ActiveController
{
    public $modelClass = NnEquipmentVlan::class;

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index', 'view', 'create', 'update', 'vlans'],
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

    /**
     * Поддерживает только GET
     */
    public function actionVlans(){
        return CVlanNet::find()->orderBy('id')->all();
    }
}