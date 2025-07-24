<?php

namespace app\controllers;

use app\readModels\EquipmentReadRepository;
use Yii;
use app\components\utils\Utils;
use app\models\AuthItem;
use app\models\User;
use app\models\reference\CVlanNet;
use app\models\reference\SOpenPort;
use app\models\registry\REquipment;
use app\models\registry\REquipmentSearch;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * EquipController implements the CRUD actions for REquipment model.
 */
class EquipController extends MainController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['index', 'view', 'get-vlan-list', 'get-model-list', 'setalleqlist'],
                            'allow' => true,
                            'roles' => ['default'],
                        ],
                        [
                            'actions' => ['create', 'update', 'delete'],
                            'allow' => true,
                            'roles' => ['manage-any-equip', 'manage-his-equip'],
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
     * Lists all REquipment models.
     * @return mixed
     */
    public function actionIndex()
    {
        set_time_limit(60);
        $searchModel = new REquipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->distinct(); // поправляет при сбоях joinWith

        $dataProvider->query->joinWith(['state0']);
        $dataProvider->query->joinWith(['type0']);
        $dataProvider->query->joinWith(['organization0']);
        $dataProvider->query->joinWith(['cVolumeHdds']);

        // $dataProvider->query->joinWith(['parent0']);
        $dataProvider->query->joinWith([
            'parent0' => function($q){
                $q->alias('r_equipment_alias');
            }
        ]);

        // $dataProvider->query->joinWith(['nnEquipmentVlans']);
        $dataProvider->query->joinWith(['nnEquipmentVlans.vlanNet']);
        $dataProvider->query->joinWith(['nnEquipmentVlans.dnsName']);

        // $dataProvider->query->joinWith(['sResponseEquipments']);
        $dataProvider->query->joinWith(['sResponseEquipments.responsePerson']);
        // $dataProvider->query->joinWith(['nnEquipmentClaims']);
        $dataProvider->query->joinWith(['nnEquipmentClaims.claim0']);
        // $dataProvider->query->joinWith(['rSoftwareInstalleds']);
        $dataProvider->query->joinWith(['rSoftwareInstalleds.software0']);

        // $dataProvider->query->joinWith(['nnEquipmentInfosysContours']);
        $dataProvider->query->joinWith(['nnEquipmentInfosysContours.contourModel']);
        $dataProvider->query->joinWith(['nnEquipmentInfosysContours.informationSystemModel']);

        // var_dump($dataProvider->models);exit;
        // Yii::$app->session->set('allEquipmentListData', false);
        if ( Yii::$app->session->get('allEquipmentListData', false) ) {
            $dataProvider->pagination = false;
        } else {
            $dataProvider->pagination = new \yii\data\Pagination(['defaultPageSize' => 30]);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSetalleqlist()
    {
        if (Yii::$app->request->isAjax) {
            if ("true" == Yii::$app->request->post('state')) {
                Yii::$app->session->set('allEquipmentListData', true);
            } else {
                Yii::$app->session->set('allEquipmentListData', false);
            }
            return;
        }
    }

    /**
     * Displays a single REquipment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        // var_dump(Yii::$app->user->can('administrator'));exit;

        if ( Yii::$app->request->isAjax ) {
            if ( Utils::userCanIn(['administrator', 'manage-pass-any-equip', 'manage-pass-his-equip']) ) {
                exit(REquipment::getPassword($id, Yii::$app->request->post('param1'), Yii::$app->request->post('param2')));
            } else {
                exit('Доступ к просмотру закрыт');
            }
        }

        // var_dump(User::getThisUserGroupsRecursive(16));exit;
        // var_dump(User::getThisUserGroupsRecursive(Yii::$app->user->id));exit;
        $accessViewPassword = false; // возможность отображения кнопок просмотра паролей

        if ( Utils::userCanIn(['administrator', 'manage-pass-any-equip']) ) {
            $accessViewPassword = true;
        } elseif ( Utils::userCanIn(['manage-pass-his-equip'])
                   && AuthItem::isEquipInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id)) ) {
            $accessViewPassword = true;
        }

        $model = $this->findModel($id);
        $repo = new EquipmentReadRepository($model);
        $informationSystems = $repo->getInformationSystems();
//        var_dump($informationSystems);exit;
        return $this->render('view-new', [
            'model' => $model,
            'accessViewPassword' => $accessViewPassword,
            'informationSystems' => $informationSystems
        ]);
    }

    public function actionGetVlanList()
    {
        if (!Yii::$app->request->isAjax) return;
        $cvlannet = CVlanNet::findOne(Yii::$app->request->post('id'));
        $html = '';
        foreach ($cvlannet->nnEquipmentVlans as $value) {
            $valName = $value->equipment0->name ?: IT_NOT_DEF;
            $html .= '<div>'
            .Html::a($valName, ['view', 'id' => $value->equipment0->id])
            ."<br>VMware: ".Html::a($value->equipment0->vmware_name ?: IT_NOT_DEF, ['view', 'id' => $value->equipment0->id])."</div>";
            foreach ($value->equipment0->nnEquipmentVlans as $k => $v) {
                $n = $k+1;
                $html .= "<span style='margin-left:30px'>ip$n ".long2ip($v->ip_address)."</span><br>";
            }
            $html .= "<br>";
        }
        return $html;
    }

    public function actionGetModelList()
    {
        if (!Yii::$app->request->isAjax) return;
        $еquipments = REquipment::find()->where(['model' => Yii::$app->request->post('name')])->all();
        $html = '';
        foreach ($еquipments as $value) {
            $valName = $value->name ?: IT_NOT_DEF;
            $html .= '<div>'
            .Html::a($valName, ['view', 'id' => $value->id])
            ."<br>VMware: ".Html::a($value->vmware_name ?: IT_NOT_DEF, ['view', 'id' => $value->id])."</div>";
            foreach ($value->nnEquipmentVlans as $k => $v) {
                $n = $k+1;
                $html .= "<span style='margin-left:30px'>ip$n ".long2ip($v->ip_address)."</span><br>";
            }
            $html .= "<br>";
        }
        return $html;
    }

    public function actionGetPortsList()
    {
        if (!Yii::$app->request->isAjax) return;
        $sopenports = SOpenPort::find()->where(['port_number' => Yii::$app->request->post('port')])->all();
        $html = '';
        foreach ($sopenports as $value) {
            $valName = $value->equipment0->name ?: IT_NOT_DEF;
            $html .= '<div>'
            .Html::a($valName, ['view', 'id' => $value->equipment0->id])
            ."<br>VMware: ".Html::a($value->equipment0->vmware_name ?: IT_NOT_DEF, ['view', 'id' => $value->equipment0->id])."</div>";
            foreach ($value->equipment0->nnEquipmentVlans as $k => $v) {
                $n = $k+1;
                $html .= "<span style='margin-left:30px'>ip$n ".long2ip($v->ip_address)."</span><br>";
            }
            $html .= "<br>";
        }
        return $html;
    }

    public function actionCreate()
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-equip', 'manage-his-equip']) ) {
            $model = new REquipment();

            // задание паролей в форме доступно только избранным
            if ( Utils::userCanIn(['administrator', 'manage-pass-any-equip', 'manage-pass-his-equip']) ) {
                $model->showPassInputInForm = true;
            }

            // добавление ответственных в форме доступно только избранным
            if ( Utils::userCanIn(['administrator', 'manage-responsepers-any-equip']) ) {
                $model->showResponsePersonsInputInForm = true;
            }

            try {
                $transaction = Yii::$app->db->beginTransaction();
                if (
                    $model->load(Yii::$app->request->post())
                    && $model->servicePrepareAmountRam()
                    && $model->save()
                    && !$model->needRollback
                ) {
                    $transaction->commit(); // если все норм то коммитим
                    // return $this->redirect(['view', 'id' => $model->id]);
                    return $this->render('create', [
                        'model' => $model,
                        'showModalSwitcher' => true,
                        'switcherId' => $model->id,
                    ]);
                } else {
                    $transaction->rollBack(); // отменяем транзакцию, поскольку нам она не нужна
                    return $this->render('create', [
                        'model' => $model,
                        'showModalSwitcher' => false,
                        'switcherId' => false,
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage();
                // следущую строку надо раскомментить, если надо увидеть реакцию, что произошло искл-е
                // иначе все пройдет по умолчанию и нас выкинет на несуществующую страницу
                // throw $e;
            }

        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    public function actionUpdate($id)
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-equip'])
             || ( Utils::userCanIn(['manage-his-equip'])
                  && AuthItem::isEquipInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))) ) {
            $model = $this->findModel($id);
// if($_POST) {var_dump($_POST);exit;}
            // редактирование паролей в форме доступно только избранным
            if ( Utils::userCanIn(['administrator', 'manage-pass-any-equip'])
                 || Utils::userCanIn(['manage-pass-his-equip'])
                    && AuthItem::isEquipInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))  ) {
                $model->showPassInputInForm = true;
            }

            // редактирование ответственных в форме доступно только избранным,
            // тут либо ты админ, либо у тебя глобальное право, либо просто оборудование
            // рекурсивно принадлежит тебе. Отдельного права на свое оборудование
            // (такого как например по паролям) нет
            if ( Utils::userCanIn(['administrator', 'manage-responsepers-any-equip'])
                 || AuthItem::isEquipInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id)) ) {
                $model->showResponsePersonsInputInForm = true;
            }

            try{
                $transaction = Yii::$app->db->beginTransaction();
                if (
                    $model->load(Yii::$app->request->post())
                    && $model->servicePrepareAmountRam()
                    && $model->save()
                    && !$model->needRollback
                ) {
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $transaction->rollBack();
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage();
            }

        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    /**
     * Deletes an existing REquipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if ( Utils::userCanIn(['administrator', 'manage-any-equip'])
             || ( Utils::userCanIn(['manage-his-equip'])
                  && AuthItem::isEquipInGroups($id, User::getThisUserGroupsRecursive(Yii::$app->user->id))) ) {

            $this->findModel($id)->delete();

            return $this->redirect(['index']);

        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    /**
     * Finds the REquipment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return REquipment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = REquipment::find()
                ->joinWith(['state0'])
                ->joinWith(['type0'])
                ->joinWith(['organization0'])
                ->joinWith(['cVolumeHdds'])
                // ->joinWith(['rPasswords'])
                ->joinWith(['nnEquipmentVlans.vlanNet'])
                ->joinWith(['nnEquipmentVlans.dnsName'])
                ->joinWith(['sResponseEquipments.responsePerson'])
                ->joinWith(['nnEquipmentClaims.claim0'])
                ->joinWith(['rSoftwareInstalleds.software0'])
                ->joinWith(['nnEquipmentInfosysContours.contourModel'])
                ->joinWith(['nnEquipmentInfosysContours.informationSystemModel'])
                ->where(["r_equipment.id" => $id])
                ->one()
            ) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }
    }
}
