<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\registry\RLog;
use app\models\registry\RLogSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;

/**
 * LogController implements the CRUD actions for RLog model.
 */
class LogController extends \app\controllers\MainController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['index','compare'],
                            'allow' => true,
                            'roles' => ['view-log', 'clear-log'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post'],
                        'compare' => ['post'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all RLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = new \yii\data\Pagination(['defaultPageSize' => 50]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'showdeleteform' => Utils::userCanIn(['administrator', 'clear-log'])
        ]);
    }

    /**
     * Displays a single RLog model.
     * @param string $id
     * @return mixed
     */
    /*public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new RLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new RLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing RLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    /*public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Deletes an existing RLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete()
    {
        // var_dump(Yii::$app->request);exit;
        $date_from = (new \Datetime(Yii::$app->request->post('from')))->format('Y-m-d H:i:s');
        $date_to = (new \Datetime(Yii::$app->request->post('to')))->format('Y-m-d H:i:s');

        if (!$date_from || !$date_to) {
            return $this->redirect(['index']);
        }
        // var_dump($date_from, $date_to);exit;

        // RLogSearch наследник RLog им и воспользуемся
        RLogSearch::deleteAll(
            'date_emergence BETWEEN :date_from AND :date_to',
            [':date_from' => $date_from, ':date_to' => $date_to]);

        return $this->redirect(['index', 'sort' => '-date_emergence']);
    }

    /**
     * Finds the RLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }
    }

    public function actionCompare()
    {
        $id = Yii::$app->request->post("id");
        $log = RLog::findOne($id);
        if (empty($log)) {
            return $this->asJson([
                "object_before" => "",
                "object_after" => "",
            ]);
        }
        return $this->asJson([
            "object_before" => nl2br($log->object_before),
            "object_after" => nl2br($log->object_after),
        ]);
    }
}
