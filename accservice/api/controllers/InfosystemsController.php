<?php
/**
 * Created by PhpStorm.
 * User: custom
 * Date: 30.08.18
 * Time: 17:40
 */

namespace app\api\controllers;


use app\models\reference\SContourInformationSystem;
use app\models\registry\RInformationSystem;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class InfosystemsController extends ActiveController
{
    public $modelClass = RInformationSystem::class;
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index','contours'],
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

    /**
     * Поддерживает только GET
     */
    public function actionContours(){
        return SContourInformationSystem::find()->orderBy('id')->all();
    }
}