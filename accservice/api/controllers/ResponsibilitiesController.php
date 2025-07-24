<?php
/**
 * Created by PhpStorm.
 * User: custom
 * Date: 29.08.18
 * Time: 17:24
 */

namespace app\api\controllers;


use app\models\reference\CVariantResponsibility;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class ResponsibilitiesController extends ActiveController
{
    public $modelClass = CVariantResponsibility::class;

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