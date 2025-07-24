<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\AuthItem;
use app\models\AuthItemSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * AuthitemController implements the CRUD actions for AuthItem model.
 */
class AuthitemController extends \app\controllers\MainController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['index', 'view', 'create', 'update', 'delete', 'rules', 'relay'],
                            'allow' => true,
                            'roles' => ['manage-user'],
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
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ( !array_key_exists('sort', Yii::$app->request->queryParams ) ) {
            $dataProvider->query->orderBy(['type' => SORT_ASC, 'alias' => SORT_ASC]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthItem model.
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
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem();

        try {
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->load(Yii::$app->request->post()) && $model->save() && !$model->needRollback) {
                $transaction->commit(); // если все норм то коммитим
                return $this->redirect(['view', 'id' => $model->name]);
            } else {
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } catch (UserException $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Updates an existing AuthItem model.
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
                return $this->redirect(['view', 'id' => $model->name]);
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
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /*
        Задание прав группе.
        Манипуляции будут производиться только в auth_item_child, поэтому смысла в транзакции здесь не будет
    */
    public function actionRules($id)
    {
        $model = $this->findModel($id);

        if ($data = Yii::$app->request->post()) {
            $model->saveAvailableUserRules($data);
            \Yii::$app->getSession()->setFlash('success', 'Данные успешно сохранены', ['delay' => 2000]);
        }
        return $this->render('rules', [
            'model' => $model,
        ]);
    }

    /*
        Задание соответствий объектов группе.
    */
    public function actionRelay($id)
    {
        $model = $this->findModel($id);

        try{
            $transaction = Yii::$app->db->beginTransaction();
            if ($data = Yii::$app->request->post()) {
                $model->saveEntytiesRelation($data);
                $transaction->commit();
                \Yii::$app->getSession()->setFlash('success', 'Данные успешно сохранены');
            }
        } catch (UserException $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->render('relay', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}
