<?php

namespace app\controllers;

use Yii;
use app\models\reference\SResponseInformationSystem;
use app\models\registry\REquipment;
use app\models\registry\RSoftware;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/*  Переменные:
    @var reportsAliases - связь названий отчетов с индексами
*/
class RepsController extends MainController
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'access' => [
                    'rules' => [
                        [
                            'actions' => ['servers-data'],
                            'allow' => true,
                            // 'roles' => ['?'],
                            'matchCallback' => function ($rule, $action) {
                                if ($action->id == 'servers-data') {
                                    // прямая авторизация по токену через заголовки
                                    if (Yii::$app->request->headers->get('spt') === 'DI8ZCfUBQ6qdA') {
                                        return true;
                                    }
                                }
                            },
                        ],
                        [
                            'actions' => ['index'],
                            'allow' => true,
                            'roles' => ['default'],
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

    public function actionIndex($id)
    {
        $nfmessage = "Запрашиваемая страница не найдена";
        try {
            if ( method_exists($this, $methodName = '_report'.$id) ) {
                $data = $this->$methodName();
                return $this->render("$id", $data);
            } else {
                throw new NotFoundHttpException($nfmessage);
            }
        } catch (\yii\base\InvalidParamException $e) {
            throw new NotFoundHttpException($nfmessage);
        }
    }

    private function _reportResppers()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => SResponseInformationSystem::find()
                ->select(
                    [
                        's_response_information_system.id',
                        's_response_information_system.information_system',
                        's_response_information_system.responsibility',
                        'concat(user.last_name, " ", user.first_name, " ", user.second_name) as responsePersonName',
                        'ris.name_short as infosystemShortName',
                        'cvr.name as responsibilityName'
                    ]
                )
                ->join('INNER JOIN', 'user', 's_response_information_system.response_person = user.id')
                ->join('INNER JOIN', 'r_information_system ris', 's_response_information_system.information_system = ris.id')
                ->join('INNER JOIN', 'c_variant_responsibility cvr', 's_response_information_system.responsibility = cvr.id'),
                'pagination' => false,
        ]);
        $dataProvider->sort->attributes['responsePersonName'] = [
            'asc' => ['user.last_name' => SORT_ASC, 'user.first_name' => SORT_ASC, 'user.second_name' => SORT_ASC],
            'desc' => ['user.last_name' => SORT_DESC, 'user.first_name' => SORT_DESC, 'user.second_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['infosystemShortName'] = [
            'asc' => ['ris.name_short' => SORT_ASC],
            'desc' => ['ris.name_short' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['responsibilityName'] = [
            'asc' => ['cvr.name' => SORT_ASC],
            'desc' => ['cvr.name' => SORT_DESC],
        ];
        if ( !array_key_exists('sort', Yii::$app->request->queryParams ) ) {
            $dataProvider->query->orderBy(['user.last_name' => SORT_ASC, 'user.first_name' => SORT_ASC, 'user.second_name' => SORT_ASC]);
        }
        return $data = ['dataProvider' => $dataProvider];
    }

    private function _reportUsedres()
    {
        $arrDataProvider = [];
        $tes = \app\models\reference\CTypeEquipment::find()->All();
        // var_dump($tes);exit;
        foreach ($tes as $k => $v) {
            $arrDataProvider[$k]['provider'] = new ActiveDataProvider([
                'query' => REquipment::find()
                    ->where(['type' => $v->id]),
                'pagination' => false,
            ]);
            $arrDataProvider[$k]['typename'] = $v->name;
            $arrDataProvider[$k]['sum_params'] = \Yii::$app->db->createCommand(
                'SELECT sum(`count_cores`) as sum_cc, sum(`count_processors`) as sum_cp, sum(`amount_ram`) as sum_ar, sum(`volume_hdd`) as sum_vh FROM r_equipment WHERE type=:type')
                ->bindValue(':type', $v->id)
                ->queryOne();
            // var_dump($arrDataProvider[$k]['sum_params']);exit;
        }
        return $data = ['arrDataProvider' => $arrDataProvider];
    }

    private function _reportUsedresonvirt()
    {
        $arrDataProvider = [];
        $parents = REquipment::find()->distinct()->select('parent')->where('parent is not null')->asArray()->All();
        // var_dump($parents);exit;
        foreach ($parents as $k => $v) {
            $arrDataProvider[$k]['provider'] = new ActiveDataProvider([
                'query' => REquipment::find()
                    ->where(['parent' => $v['parent']]),
                'pagination' => false,
            ]);
            $arrDataProvider[$k]['parent'] = REquipment::findOne($v['parent']);
            $arrDataProvider[$k]['sum_params'] = \Yii::$app->db->createCommand(
                'SELECT sum(`count_cores`) as sum_cc, sum(`count_processors`) as sum_cp, sum(`amount_ram`) as sum_ar, sum(`volume_hdd`) as sum_vh FROM r_equipment WHERE parent=:parent')
                ->bindValue(':parent', $v['parent'])
                ->queryOne();
            // var_dump($arrDataProvider[$k]['sum_params']);exit;
        }
        return $data = ['arrDataProvider' => $arrDataProvider];
    }

    private function _reportUsedlicenses()
    {
        $arrDataProvider = [];
        $tes = \app\models\reference\CTypeSoftware::find()->All();
        // var_dump($tes);exit;
        foreach ($tes as $k => $v) {
            $arrDataProvider[$k]['provider'] = new ActiveDataProvider([
                'query' => RSoftware::find()
                    ->select(['*', 'helperproperty'=>'(select count(`r_software_installed`.`id`) from `r_software_installed` where `software` =  `r_software`.`id`)'])
                    ->where(['type' => $v->id]),
                'pagination' => false,
                /*'sort' => [
                    'attributes' => [
                        'helperproperty' => [
                            'asc' => ['helperproperty' => SORT_ASC],
                            'desc' => ['helperproperty' => SORT_DESC],
                        ]
                    ]
                ]*/ //так работает, но уничтожает все остальные, поэтому добавляем отдельно
            ]);
            $arrDataProvider[$k]['provider']->sort->attributes['helperproperty'] = [
                'asc' => ['helperproperty' => SORT_ASC],
                'desc' => ['helperproperty' => SORT_DESC],
            ];
            $arrDataProvider[$k]['typename'] = $v->name;
            // var_dump($arrDataProvider[$k]['sum_params']);exit;
        }
        return $data = ['arrDataProvider' => $arrDataProvider];
    }

    private function _reportStatearcsight()
    {
        $arrDataProvider = [];
        $tes = \app\models\reference\CTypeEquipment::find()->All();
        // var_dump($tes);exit;
        foreach ($tes as $k => $v) {
            $arrDataProvider[$k]['provider'] = new ActiveDataProvider([
                'query' => REquipment::find()
                    ->where(['type' => $v->id]),
                'pagination' => false,
            ]);
            $arrDataProvider[$k]['typename'] = $v->name;
            $arrDataProvider[$k]['sum_params'] = \Yii::$app->db->createCommand(
                'SELECT count(`id`) as count_arc FROM r_equipment WHERE type=:type and `connect_arcsight` is not null and `connect_arcsight` != 0')
                ->bindValue(':type', $v->id)
                ->queryOne();
            // var_dump($arrDataProvider[$k]['sum_params']);exit;
        }
        return $data = ['arrDataProvider' => $arrDataProvider];
    }

    public function actionServersData()
    {
        $query = "
               SELECT -- eq.id,
                      type_eq.name as `тип`, eq.name as `наименование`, eq.vmware_name as `vmware-имя`, eq.description as `описание`, org.name as `владелец`,
                      eq.count_cores as `количество ядер шт`, eq.amount_ram as `озу мб`,
                      (
                          SELECT GROUP_CONCAT(cvh.size)
                            FROM accservice_schema.c_volume_hdd cvh
                           WHERE cvh.equipment = eq.id
                      ) as `пзу мб`,
                      (
                          SELECT GROUP_CONCAT( inet_ntoa( nqv.ip_address ) )
                            FROM accservice_schema.nn_equipment_vlan nqv
                           WHERE nqv.equipment = eq.id
                      ) as `ip-адреса`,
                      (
                          SELECT GROUP_CONCAT(concat(user.last_name, ' ',user.first_name, ' (',user.email,')'))
                            FROM accservice_schema.s_response_equipment sre
                       LEFT JOIN accservice_schema.user ON user.id = sre.response_person
                           WHERE sre.equipment = eq.id AND sre.responsibility = 6
                      ) as `отв. за администрирование оборудования`,
                      (
                          SELECT GROUP_CONCAT(concat(user.last_name, ' ',user.first_name, ' (',user.email,')'))
                            FROM accservice_schema.s_response_equipment sre
                       LEFT JOIN accservice_schema.user ON user.id = sre.response_person
                           WHERE sre.equipment = eq.id AND sre.responsibility = 5
                      ) as `отв. за администрирование ОС и ПО`,
                      (
                          SELECT GROUP_CONCAT(concat(user.last_name, ' ',user.first_name, ' (',user.email,')'))
                            FROM accservice_schema.s_response_equipment sre
                       LEFT JOIN accservice_schema.user ON user.id = sre.response_person
                           WHERE sre.equipment = eq.id AND sre.responsibility = 4
                      ) as `отв. за консультирование и ведение проекта`,
                      (
                          SELECT GROUP_CONCAT(concat(user.last_name, ' ',user.first_name, ' (',user.email,')'))
                            FROM accservice_schema.s_response_equipment sre
                       LEFT JOIN accservice_schema.user ON user.id = sre.response_person
                           WHERE sre.equipment = eq.id AND sre.responsibility = 3
                      ) as `отв. за информационную безопасность`,
                      (
                          SELECT GROUP_CONCAT(soft.name)
                            FROM accservice_schema.r_software_installed sins
                       LEFT JOIN accservice_schema.r_software soft ON soft.id = sins.software
                           WHERE sins.equipment = eq.id
                      ) as `установленное по`,
                      (
                          SELECT IF( GROUP_CONCAT(pass.password) IS NOT NULL , 'да', 'нет')
                            FROM accservice_schema.r_password pass
                           WHERE pass.equipment = eq.id
                      ) as `есть ли пароли`,
                      state_eq.name as `статус`,
                      IF(eq.backuping, 'да', 'нет') as `бекапирование`,
                      (
                          SELECT GROUP_CONCAT(scl.name)
                            FROM accservice_schema.nn_equipment_claim nec
                       LEFT JOIN accservice_schema.s_claim scl ON nec.claim = scl.id
                           WHERE nec.equipment = eq.id
                      ) as `заявки`,
                      (
                          SELECT GROUP_CONCAT(cag.name)
                            FROM accservice_schema.nn_equipment_claim nec
                       LEFT JOIN accservice_schema.s_claim scl ON scl.id = nec.claim
                       LEFT JOIN accservice_schema.c_agreement cag ON cag.id = scl.agreement
                           WHERE nec.equipment = eq.id
                      ) as `соглашения`
                 FROM accservice_schema.r_equipment eq
            LEFT JOIN accservice_schema.s_organization org ON eq.organization = org.id
            LEFT JOIN accservice_schema.c_type_equipment type_eq ON eq.type = type_eq.id
            LEFT JOIN accservice_schema.c_state_equipment state_eq ON eq.state = state_eq.id;
        ";
        $result = Yii::$app->db->createCommand($query)->queryAll();
        $this->serverReportFileFullName = sys_get_temp_dir().'/serversdata.csv';
        // var_dump($this->serverReportFileFullName);exit;
        $fp = fopen($this->serverReportFileFullName, 'w');
        foreach ($result as $fields) {
            foreach ($fields as &$field) {
                $field = mb_convert_encoding($field, "cp-1251", "utf-8");
            }
            fputcsv($fp, $fields, ";", '"', "\\");
        }
        fclose($fp);
        return Yii::$app->response->sendFile($this->serverReportFileFullName);
    }

    public $serverReportFileFullName;
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if ($action->id === 'servers-data')
        {
            @unlink($this->serverReportFileFullName);
            Url::previous();
        }
        return $result;
    }
}
