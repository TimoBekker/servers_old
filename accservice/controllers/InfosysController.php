<?php

namespace app\controllers;

use Yii;
use app\models\registry\RInformationSystem;
use app\models\registry\RInformationSystemSearch;
use app\models\AuthItem;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;
use yii\base\UserException;

/**
 * InfosysController implements the CRUD actions for RInformationSystem model.
 */
class InfosysController extends MainController
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
                            'roles' => ['manage-any-infosys', 'manage-his-infosys'],
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
     * Lists all RInformationSystem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RInformationSystemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->distinct(); // поправляет при сбоях joinWith
        $dataProvider->query->joinWith(['nnEquipmentInfosysContours.equipmentModel']);

        if ( !array_key_exists('sort', Yii::$app->request->queryParams ) ) {
            $dataProvider->query->orderBy = ['name_short' => 'asc'];
        }


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RInformationSystem model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if(Yii::$app->request->isAjax){
            if ( Utils::userCanIn(['administrator', 'manage-pass-any-infosys', 'manage-pass-his-infosys']) ) {
                exit(RInformationSystem::getPassword($id, Yii::$app->request->post('param1'), Yii::$app->request->post('param2')));
            } else {
                exit('Доступ к просмотру закрыт');
            }
        }

        // var_dump(User::getThisUserGroupsRecursive(16));exit;
        // var_dump(User::getThisUserGroupsRecursive(Yii::$app->user->id));exit;
        $accessViewPassword = false; // возможность отображения кнопок просмотра паролей

        if ( Utils::userCanIn(['administrator', 'manage-pass-any-infosys']) ) {
            $accessViewPassword = true;
        } elseif ( Utils::userCanIn(['manage-pass-his-infosys'])
                   && AuthItem::isInfosysInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id)) ) {
            $accessViewPassword = true;
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'accessViewPassword' => $accessViewPassword,
        ]);
    }

    /**
     * Creates a new RInformationSystem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-infosys', 'manage-his-infosys']) ) {

            $model = new RInformationSystem();

            // задание паролей в форме доступно только избранным
            if ( Utils::userCanIn(['administrator', 'manage-pass-any-infosys', 'manage-pass-his-infosys']) ) {
                $model->showPassInputInForm = true;
            }

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

        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    /**
     * Updates an existing RInformationSystem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-infosys'])
             || ( Utils::userCanIn(['manage-his-infosys'])
                  && AuthItem::isInfosysInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))) ) {

            $model = $this->findModel($id);

            // редактирование паролей в форме доступно только избранным
            if ( Utils::userCanIn(['administrator', 'manage-pass-any-infosys'])
                 || Utils::userCanIn(['manage-pass-his-infosys'])
                    && AuthItem::isInfosysInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))  ) {
                $model->showPassInputInForm = true;
            }

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
     * Deletes an existing RInformationSystem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id, $dsc = false, $dsic = false)
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-infosys'])
             || ( Utils::userCanIn(['manage-his-infosys'])
                  && AuthItem::isInfosysInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))) ) {

            // $this->findModel($id)->delete();

            $dsc = (bool) $dsc;
            $dsic = (bool) $dsic;
            // удаляемая модель
            $deletedModel = $this->findModel($id);

            try{
                $transaction = Yii::$app->db->beginTransaction();
                // Удаление дистрибутивов ПО (если надо)
                if ($dsc) {
                    foreach ($deletedModel->nnIsSoftwares as $val) {
                        $val->software0->delete();
                    }
                }
                // Удаление установленного ПО (если надо)
                if ($dsic) {
                    foreach ($deletedModel->nnIsSoftinstalls as $val) {
                        $val->softwareInstalled->delete();
                    }
                }
                $deletedModel->delete();
                $transaction->commit();
            } catch (UserException $e) {
                $transaction->rollBack();
                throw $e;
            }

            return $this->redirect(['index']);

        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    /**
     * Finds the RInformationSystem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RInformationSystem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RInformationSystem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }
    }
}
