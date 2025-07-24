<?php

namespace app\controllers;

use Yii;
use app\models\registry\RSoftwareInstalled;
use app\models\registry\RSoftwareInstalledSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\base\UserException;

/**
 * SoftinsController implements the CRUD actions for RSoftwareInstalled model.
 */
class SoftinsController extends MainController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['index', 'view'],
                            'allow' => true,
                            'roles' => ['default'],
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['manage-any-equip', 'manage-his-equip', 'manage-any-soft', 'manage-his-soft'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['post'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all RSoftwareInstalled models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RSoftwareInstalledSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = new \yii\data\Pagination(['defaultPageSize' => 30]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RSoftwareInstalled model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new RSoftwareInstalled model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RSoftwareInstalled();

        try {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->load(Yii::$app->request->post()) && $model->save() && !$model->needRollback) {
                $transaction->commit(); // если все норм то коммитим
                // return $this->redirect(['view', 'id' => $model->id]);
                return $this->render('create', [
                    'model' => $model,
                    'showModalSwitcher' => true,
                    'switcherId' => $model->id,
                ]);
            } else {
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                    'showModalSwitcher' => false,
                    'switcherId' => false,
                ]);
            }
        } catch (UserException $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Updates an existing RSoftwareInstalled model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        try{
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->load(Yii::$app->request->post()) && $model->save() && !$model->needRollback) {
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                $transaction->rollBack();
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } catch (UserException $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Deletes an existing RSoftwareInstalled model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the RSoftwareInstalled model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RSoftwareInstalled the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RSoftwareInstalled::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }
    }
}
