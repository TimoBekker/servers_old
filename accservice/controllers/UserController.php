<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends \app\controllers\MainController
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
                            'roles' => [
                                'manage-contract-npa',
                                'manage-any-soft',
                                'manage-his-soft',
                                'manage-any-equip',
                                'manage-his-equip',
                                'manage-any-infosys',
                                'manage-his-infosys'
                            ],
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ( !array_key_exists('sort', Yii::$app->request->queryParams ) ) {
            $dataProvider->query->orderBy = ['last_name' => 'asc', 'first_name' => 'asc', 'second_name' => 'asc'];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'notdeleted' => Yii::$app->request->get('notdeleted') ? true : false,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // if ( Utils::userCanIn(['administrator', 'manage-contract-npa']) ) {
            $model = new User();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        // } else {
        //     throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        // }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        // if ( Utils::userCanIn(['administrator', 'manage-contract-npa']) ) {
            $model = $this->findModel($id);

            // исключим возможность доступа к редактированию системных пользователей
            if ( !empty($model->username) || !empty($model->password_hash) ) {
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
            }

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        // } else {
        //     throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        // }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // if ( Utils::userCanIn(['administrator', 'manage-contract-npa']) ) {
            // $this->findModel($id)->delete();

            // return $this->redirect(['index']);

            // исключим возможность доступа к удалению системных пользователей
            if ( !empty($model->username) || !empty($model->password_hash) ) {
                throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
            }

            $model = $this->findModel($id);

            if ( !$model->deleteWithCheckConstraint() )
                return $this->redirect(['index', 'notdeleted' => true]);
            else
                return $this->redirect(['index']);
        // } else {
        //     throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        // }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }
    }
}
