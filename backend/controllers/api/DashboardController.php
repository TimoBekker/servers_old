<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
use app\models\registry\REquipment;
use app\models\registry\RInformationSystem;
use app\models\registry\RSoftware;
use app\models\registry\RContract;
use app\models\registry\REvent;

class DashboardController extends Controller
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
            'except' => ['options'],
        ];

        return $behaviors;
    }

    public function actionStats()
    {
        return [
            'equipment' => REquipment::find()->count(),
            'informationSystems' => RInformationSystem::find()->count(),
            'software' => RSoftware::find()->count(),
            'contracts' => RContract::find()->count(),
        ];
    }

    public function actionEvents()
    {
        $events = REvent::find()
            ->with(['type0'])
            ->orderBy('date_begin DESC')
            ->limit(5)
            ->all();

        return array_map(function($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'description' => $event->description,
                'date_begin' => $event->date_begin,
                'date_end' => $event->date_end,
                'type' => $event->type0 ? $event->type0->name : null,
            ];
        }, $events);
    }

    public function actionOptions()
    {
        return [];
    }
}