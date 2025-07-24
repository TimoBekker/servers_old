<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\AuthItem;
use app\models\registry\RSoftware;
use app\models\registry\RSoftwareSearch;
use yii\base\UserException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;

/**
 * SoftController implements the CRUD actions for RSoftware model.
 */
class SoftController extends MainController
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
                            'roles' => ['manage-any-soft', 'manage-his-soft'],
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
     * Lists all RSoftware models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RSoftwareSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ( !array_key_exists('sort', Yii::$app->request->queryParams ) ) {
            $dataProvider->query->orderBy = ['c_type_software.name' => SORT_ASC, 'r_software.name' => SORT_ASC, 'version' => SORT_ASC];
        }
        $dataProvider->pagination = new \yii\data\Pagination(['defaultPageSize' => 30]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RSoftware model.
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
     * Creates a new RSoftware model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RSoftware();

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
     * Updates an existing RSoftware model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-soft'])
             || ( Utils::userCanIn(['manage-his-soft']) && AuthItem::isSoftInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))) ) {

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
        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    /**
     * Deletes an existing RSoftware model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-soft'])
             || ( Utils::userCanIn(['manage-his-soft']) && AuthItem::isSoftInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))) ) {

            $this->findModel($id)->delete();

            return $this->redirect(['index']);
        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    /**
     * Finds the RSoftware model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RSoftware the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RSoftware::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }
    }
}
