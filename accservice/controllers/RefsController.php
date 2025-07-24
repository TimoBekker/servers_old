<?php
/* контроллер управления справочниками */
namespace app\controllers;

use Yii;
use app\models\reference\CTypeEquipment;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\helpers\ArrayHelper;
use app\components\utils\Utils;

class RefsController extends MainController
{
    public static $modelsNamespaceAliace = 'app\\models\\reference\\';

    // выставляем в нужной нам очередности справочники, а также
    // РАЗДЕЛИТЕЛИ в качесве Ключей со значением-классом separator
    public static $refsnames = [
        'Оборудование' => 'separator equip',
        'CTypeEquipment',
        'SPlacement',
        'CVlanNet',
        'SIpdnsTable',
        // 'SOpenPort',
        'CProtocol',
        'SVariantAccessRemote',
        'CTypeAccessRemote',
        'Заявки и соглашения' => 'separator claims',
        'SClaim',
        'CAgreement',
        'Информационные системы' => 'separator security',
        'CVariantValidation',
        'CLevelProtection',
        'CLevelPrivacy',
        'SContourInformationSystem',
        'Программное обеспечение' => 'separator soft',
        'CTypeSoftware',
        'CTypeLicense',
        'CMethodLicense',
        'Нормативно-правовые акты' => 'separator doclegal',
        'CTypeLegalDoc',
        'Мероприятия, работы, события' => 'separator docevent',
        'CTypeEvent',
        'Люди и организации' => 'separator orgresp',
        'SOrganization',
        'CVariantResponsibility',
        // 'SResponseEquipment',
        // 'SResponseInformationSystem',
        // 'SResponseWarrantySupport',
        // 'SContractor',
    ];

    public static $viewedRefs = [
        'SClaim',
        'CAgreement',
        'SOrganization',
    ];

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

    public function actionIndex()
    {
        // если мы выбрали справочник, т.е. url вида http://accountservice.local/web/index.php?r=refs%2Findex&refid=typeequipment
        // выводим индексную страницу справочника
        // var_dump(Yii::$app->request->get()['refid']);exit;
        if(!empty(Yii::$app->request->get()['refid'])){
            $refname = Yii::$app->request->get()['refid'];
            $classname = self::$modelsNamespaceAliace.$refname;
            if(class_exists($classname) && in_array($refname, self::$refsnames))
                $dataProvider = new ActiveDataProvider([
                    'query' => $classname::find(),
                ]);
            else
                throw new BadRequestHttpException('Запрашиваемого справочника не существует');

            return $this->render('index', [
                'classname' => $classname,
                'refname' => $refname,
                'refTitle' => $classname::$tableLabelName,
                'dataProvider' => $dataProvider,
                'notdeleted' => Yii::$app->request->get('notdeleted') ? true : false,
            ]);
        }

        // сделаем хак в ArrayDataProvider (используем его здесь вместо ActiveDataProvider), введем данные вручную, без запроса к базе
        // $query = new \yii\db\Query;
        foreach (self::$refsnames as $key => $value) {
            // if($value == 'separator'){
            if(mb_strpos($value, 'separator') !== false){
                $allModels[] = ['name' => "<div class='{$value} text-center'><strong>{$key}<br/><span class='glyphicon glyphicon-collapse-down'></span></strong></div>"];
                $ui_class_name = $value;
                continue;
            }
            $classname = self::$modelsNamespaceAliace.$value; // так сделается подстановка
            $url = \Yii::$app->urlManager->createUrl(['refs/index/', 'refid' => $value]);
            $allModels[] = ['name' => "<a href='{$url}' class='{$ui_class_name} need-closed'>".$classname::$tableLabelName.'</a>'];
        }

        // поскольку решение о добавлении в справочники Ответственных лиц (User) пришло позно, то добавляем данную
        // модель отдельным решением. В массив вывода справочников добавим здесь так:
        $urlRp = \Yii::$app->urlManager->createUrl(['user/index/']);
        $temp[] = ['name' => "<a href='{$urlRp}' class='separator orgresp need-closed'>Ответственные лица</a>"];
        array_splice($allModels, -1, 0, $temp);

        $refsnames = new ArrayDataProvider([
            // 'allModels' => $query->from('c_type_equipment')->all(),
            'allModels' => $allModels,
            'pagination' => new \yii\data\Pagination(['defaultPageSize' => 30]),
        ]);
        // $dataProvider = new ArrayDataProvider();
        return $this->render('metaindex', [
            'refsnames' => $refsnames,
        ]);
    }

    public function actionView($refid, $id)
    {
        if(!in_array($refid, self::$viewedRefs))
            throw new BadRequestHttpException('Запрашиваемого справочника не существует');

        $model = $this->findModel($refid, $id);

        return $this->render('view', [
            'refid' => $refid,
            'model' => $model,
        ]);
    }

    /*
     * @var $refid - идентификатор справочника
     * */
    private function getAccessRules($refid):bool
    {
        return
            Utils::userCanIn(['administrator'])
            ||
            (
                Utils::userCanIn(['manage-contract-npa'])
                &&
                (
                    $refid === 'CTypeLegalDoc'
                    || $refid === 'SOrganization'
                    || $refid === 'CVariantResponsibility'
                    || $refid === 'SClaim'
                    || $refid === 'CAgreement'
                )
            )
            ||
            (
                Utils::userCanIn(['manage-any-soft', 'manage-his-soft'])
                &&
                (
                    $refid === 'CTypeSoftware'
                    || $refid === 'CTypeLicense'
                    || $refid === 'CMethodLicense'
                    || $refid === 'CTypeEvent'
                    || $refid === 'SOrganization'
                    || $refid === 'CVariantResponsibility'
                )
            )
            ||
            (
                Utils::userCanIn(['manage-any-equip', 'manage-his-equip'])
                &&
                (
                    $refid === 'CTypeEquipment'
                    || $refid === 'SPlacement'
                    || $refid === 'CVlanNet'
                    || $refid === 'SIpdnsTable'
                    || $refid === 'SVariantAccessRemote'
                    || $refid === 'CTypeAccessRemote'
                    || $refid === 'SClaim'
                    || $refid === 'CAgreement'
                    || $refid === 'CTypeEvent'
                    || $refid === 'SOrganization'
                    || $refid === 'CVariantResponsibility'
                )
            )
            ||
            (
                Utils::userCanIn(['manage-any-infosys', 'manage-his-infosys'])
                &&
                (
                    $refid === 'CVariantValidation'
                    || $refid === 'CLevelProtection'
                    || $refid === 'CLevelPrivacy'
                    || $refid === 'SContourInformationSystem'
                    || $refid === 'CTypeEvent'
                    || $refid === 'SOrganization'
                    || $refid === 'CVariantResponsibility'
                )
            );
    }

    /*
     * @var $refid - идентификатор справочника
     * */
    public function actionCreate($refid)
    {
        if ( $this->getAccessRules($refid) ) {
            // var_dump($refid);exit;
            $classname = self::$modelsNamespaceAliace.$refid; // так сделается подстановка
            $model = new $classname();

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index', 'refid' => $refid]);
            } else {
                return $this->render('create', [
                    'refid' => $refid,
                    'model' => $model,
                ]);
            }
        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    public function actionUpdate($refid, $id)
    {
        if ( $this->getAccessRules($refid) ) {
            // var_dump($refid);exit;
            $model = $this->findModel($refid, $id);

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index', 'refid' => $refid]);
            } else {
                return $this->render('update', [
                    'refid' => $refid,
                    'model' => $model,
                ]);
            }
        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    public function actionDelete($refid, $id)
    {
        if ( $this->getAccessRules($refid) ) {
            /*
                Информация из справочников удаляется только в случае, если на неё не существует связей.
                В противном случае пользователь должен получить предупреждение о невозможности удаления
            */

            // $this->findModel($refid, $id)->delete();

            // return $this->redirect(['index', 'refid' => $refid, 'notdeleted' => true]);

            $model = $this->findModel($refid, $id);

            if ( !$model->deleteWithCheckConstraint() )
                return $this->redirect(['index', 'refid' => $refid, 'notdeleted' => true]);
            else
                return $this->redirect(['index', 'refid' => $refid]);
        } else {
            throw new ForbiddenHttpException('Вам не разрешено производить данное действие');
        }
    }

    protected function findModel($refid, $id)
    {
        $classname = self::$modelsNamespaceAliace.$refid; // так сделается подстановка
        if (($model = $classname::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }
    }
}
