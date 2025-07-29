<?php

namespace app\controllers\api;

use Yii;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
use yii\data\ActiveDataProvider;
use app\models\registry\REquipment;
use app\models\reference\CTypeEquipment;
use app\models\reference\CStateEquipment;

class EquipmentController extends ActiveController
{
    public $modelClass = 'app\models\registry\REquipment';

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

    public function actions()
    {
        $actions = parent::actions();
        
        // Переопределяем действие index для кастомной пагинации
        unset($actions['index']);
        
        return $actions;
    }

    public function actionIndex()
    {
        $query = REquipment::find()
            ->joinWith(['state0'])
            ->joinWith(['type0'])
            ->joinWith(['organization0']);

        // Применяем фильтры
        $request = Yii::$app->request;
        
        if ($name = $request->get('name')) {
            $query->andWhere(['like', 'r_equipment.name', $name]);
        }
        
        if ($type = $request->get('type')) {
            $query->andWhere(['r_equipment.type' => $type]);
        }
        
        if ($state = $request->get('state')) {
            $query->andWhere(['r_equipment.state' => $state]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $request->get('per-page', 30),
                'page' => $request->get('page', 1) - 1,
            ],
        ]);

        return $dataProvider;
    }

    public function actionTypes()
    {
        return CTypeEquipment::find()->orderBy('id')->all();
    }

    public function actionStates()
    {
        return CStateEquipment::find()->orderBy('id')->all();
    }

    public function actionPasswords($id)
    {
        $model = REquipment::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Оборудование не найдено");
        }

        $passes = $model->rPasswords;
        $actual = [];
        foreach ($passes as $value) {
            if ($value->finale || $value->next) continue;
            unset($value->id);
            unset($value->next);
            unset($value->finale);
            unset($value->information_system);
            unset($value->equipment);
            $actual[] = $value;
        }
        return $actual;
    }
}