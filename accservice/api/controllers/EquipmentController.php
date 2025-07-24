<?php

namespace app\api\controllers;

use app\components\utils\Cryptonite;
use app\models\reference\CStateEquipment;
use app\models\reference\CTypeEquipment;
use Yii;
use yii\filters\auth\HttpHeaderAuth;
use yii\filters\RateLimiter;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\rest\ActiveController;
use app\models\registry\REquipment;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class EquipmentController extends ActiveController
{
    public $modelClass = REquipment::class;

    public function behaviors()
    {
        return [
            // ...
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'only' => ['index', 'view', 'types', 'states'],
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

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionCreate()
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        $equipment = new REquipment();
        $equipment->WORK_IN_API_ENV = true;
        $equipment->scenario = 'work_in_api_env';
        $equipment->load($params,'');
        if ($equipment->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            return Json::encode($equipment);
        } elseif (!$equipment->hasErrors()) {
            throw new ServerErrorHttpException('Невозможно создать объект по неизвестной причине');
        }
        return Json::encode($equipment->errors);
    }

    public function actionUpdate(int $id)
    {
        $params = Yii::$app->getRequest()->getBodyParams();
        $equipment = REquipment::findOne($id);
        if (is_null($equipment)) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(404);
            return;
        }
        $equipment->WORK_IN_API_ENV = true;
        $equipment->scenario = 'work_in_api_env';
        $equipment->load($params,'');
        if ($equipment->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(200);
            return Json::encode($equipment);
        } elseif (!$equipment->hasErrors()) {
            throw new ServerErrorHttpException('Невозможно обновить объект по неизвестной причине');
        }
        return Json::encode($equipment->errors);
    }


    /**
     * Поддерживает только GET
     */
    public function actionTypes(){
        return CTypeEquipment::find()->orderBy('id')->all();
    }

    /**
     * Поддерживает только GET
     */
    public function actionStates(){
        return CStateEquipment::find()->orderBy('id')->all();
    }
//    public function behaviors()
//    {
//        // удаляем rateLimiter, требуется для аутентификации пользователя
//        $behaviors = parent::behaviors();
//        unset($behaviors['rateLimiter']);
//        return $behaviors;
//    }

    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionPasswords($id){
        $model = REquipment::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Данные не найдены");
        }
        $passes = $model->rPasswords;
        $actual = [];
        foreach ($passes as $value) {
            if ( $value->finale || $value->next ) continue;
            unset($value->id);
            unset($value->next);
            unset($value->finale);
            unset($value->information_system);
            unset($value->equipment);
            $value->password = Cryptonite::decodePassword($value->password);
            $actual[] = $value;
        }
        return $actual;
    }
}