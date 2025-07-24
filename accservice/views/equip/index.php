<?php

use app\models\reference\CStateEquipment;
use app\models\reference\CTypeEquipment;
use app\models\reference\CVlanNet;
use app\models\reference\SClaim;
use app\models\reference\SOrganization;
use app\models\registry\REquipment;
use app\models\registry\RSoftware;
use app\readModels\EquipmentReadRepository;
use kartik\select2\Select2;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\REquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerCss("
    #showedcolumnsetup {
    height: 200px;
    overflow: auto;
    background: #f9f9f9;
    }
");

$this->title = 'Оборудование';
$this->params['breadcrumbs'][] = $this->title;

$yesNo = function($attrName){
    return function($model) use ($attrName) {
        if (!empty($model->$attrName)) {
            return 'Есть';
        }
        return 'Нет';
    };
};
$defaultSelectedShowlist = [0,2,4,5,6,7,15];
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="requipment-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Добавить оборудование', ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::checkbox('maxscreen', false, ['id'=>'maxscreen','onchange' => '
            var iam = $("#maxscreen");
            $(".container").addClass("containered");
            $(".containered").toggleClass("container");
            if ($(".containered").hasClass("container")) {
                $("#equipments-list").attr("style", "overflow-x: auto");
                localStorage.setItem("requipmentIndexWide", "false");
            } else {
                $("#equipments-list").removeAttr("style");
                localStorage.setItem("requipmentIndexWide", "true");
            }
        ',
        'style'=>'margin-left:20px']);

        $this->registerJs('
            if (localStorage.getItem("requipmentIndexWide") === "true") {
                $("#maxscreen").change().attr("checked","true");
            }', \yii\web\View::POS_READY);
        ?>

        <?= Html::label('Рабочая область по ширине экрана', 'maxscreen',['class'=>'btn btn-default']); ?>

        <?= Html::checkbox('showalllist', Yii::$app->session->get('allEquipmentListData', false), [
            'id'=>'showalllist',
            'style'=>'margin-left:20px',
            'onchange'=>'

                $.post("'.Url::to(['equip/setalleqlist']).'",
                    {
                        "state" : $("#showalllist").prop("checked")
                    }, function(data, textStatus, xhr) {
                    location.reload();
                });
            '
        ]); ?>
        <?= Html::label('Выводить полный список','showalllist',['class'=>'btn btn-default']); ?>

        <?= Html::button("Задать столбцы для отображения", ['class'=>'btn btn-primary','style'=>'margin-left:20px',
        'onclick'=>'$("#showedcolumnsetup").toggle()']);
        ?>
    </p>
    <p>

        <?= Html::checkboxList(null, $defaultSelectedShowlist, [
            "Порядковый номер",
            "Статус",
            "Тип",
            "Модель",
            "Hostname (имя в OS)",
            "VMware-имя",
            "IP-адреса",
            "VLAN",
            "DNS-имена",
            "Процессоров",
            "Ядер CPU",
            "ОЗУ, Mb",
            "HDD, Mb",
            "Описание",
            "Организация-владелец",
            "Ответственные",
            "Программное обеспечение",
            "Доступ в КСПД",
            "Доступ в Интернет",
            "Соединение с Arcsight",
            "Заявки",
            "Родительское оборудование",
            "Бекапирование",
            "Действия",
            "Информационные системы",
            "Контур",
        ], ['id' => 'showedcolumnsetup', 'separator' => '<br>', 'style'=>'padding-left:20px;display:none']);
        $this->registerJs('
            function setCheckedList(dataList) {
                var list = $("#showedcolumnsetup input");
                list.prop("checked", false);
                dataList.forEach(function(el, ind, arr) {
                    $(list[el]).prop("checked", true);
                });
            }
            function getCheckedList() {
                var list = $("#showedcolumnsetup input");
                var dataList = [];
                list.each(function(key, el) {
                    if ($(el).prop("checked")) {
                        dataList.push(key);
                    }
                });
                return dataList;
            }
            function showColumns(dataList) {
                $("[data-index]").hide();
                dataList.forEach(function(el, ind, arr) {
                    $("[data-index=\""+el+"\"]").show();
                });
            }
            $("#showedcolumnsetup input").change(function(event) {
                var dataList = getCheckedList();
                showColumns(dataList);
                localStorage.setItem("checkedColumnList",JSON.stringify(dataList));
            });
            if (localStorage.getItem("checkedColumnList") === null) {
                showColumns('.json_encode($defaultSelectedShowlist).');
            } else {
                setCheckedList(JSON.parse(localStorage.getItem("checkedColumnList")));
                $("#showedcolumnsetup input").change();
            }
        ', \yii\web\View::POS_READY);
        ?>
    </p>

    <?= GridView::widget([
        'id' => 'equipments-list',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['style'=>'overflow-x:scroll','class' => 'grid-view'],
        // 'headerRowOptions'=>[],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
            'contentOptions' => ['data-index'=>0],
            'filterOptions' => ['data-index'=>0],
            'headerOptions' => ['data-index'=>0],
            ],

            // 'id',
            [
                'attribute' => 'state',
                'label' => 'Статус',
                'value' => 'state0.name',
                'contentOptions' => ['nowrap'=>true, 'data-index'=>1],
                'filterOptions' => ['data-index'=>1],
                'headerOptions' => ['data-index'=>1],
                'filter' => CStateEquipment::find()->select(['name','id'])->indexBy('id')->column(),
                /*'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'state',
                    'data' => CStateEquipment::find()->select(['name','id'])->indexBy('id')->column(),
                    'theme' => Select2::THEME_BOOTSTRAP,
                    // 'hideSearch' => true,
                    'options' => [
                        'placeholder' => 'Wybierz typ obiektu...',
                    ]
                ]),*/
            ],
            'type'=>[
                'attribute' => 'type',
                // 'label'=>'Альтернативный загловок столбца',
                // 'value' => function($model){
                //     return $model->type0->name;
                // },
                'value' => 'type0.name',
                'filter' => ArrayHelper::map(CTypeEquipment::find()->select('id, name')->asArray()->All(), 'id', 'name'),
                'contentOptions' => ['data-index'=>2],
                'filterOptions' => ['data-index'=>2],
                'headerOptions' => ['data-index'=>2],
            ],
            [
                'attribute' => 'model',
                'label' => 'Модель',
                'filter' => REquipment::find()->select(['model'])->where('model != ""')->indexBy('model')->column(),
                'contentOptions' => ['data-index'=>3],
                'filterOptions' => ['data-index'=>3],
                'headerOptions' => ['data-index'=>3],
            ],
            'name' => [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->name, ['equip/view', 'id' => $model->id]);
                },
                'contentOptions' => ['data-index'=>4],
                'filterOptions' => ['data-index'=>4],
                'headerOptions' => ['data-index'=>4],
            ],
            [
                'attribute' => 'vmware_name',
                'format' => 'html',
                'label' => 'VMware-имя',
                'contentOptions' => ['data-index'=>5],
                'filterOptions' => ['data-index'=>5],
                'headerOptions' => ['data-index'=>5],
            ],
            [
                'attribute' => 'ip_address',
                'label' => 'IP-адреса',
                'format' => 'html',
                'value' => function($model) {
                    if (!isset($model->nnEquipmentVlans)) return null;
                    $res = '';
                    foreach ($model->nnEquipmentVlans as $item) {
                        $tmp = long2ip($item->ip_address);
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'contentOptions' => ['data-index'=>6],
                'filterOptions' => ['data-index'=>6],
                'headerOptions' => ['data-index'=>6],
            ],
            [
                'attribute' => 'vlan_name',
                'label' => 'VLAN',
                'format' => 'html',
                'value' => function($model) {
                    if (!isset($model->nnEquipmentVlans)) return null;
                    $res = '';
                    foreach ($model->nnEquipmentVlans as $item) {
                        if (!isset($item->vlanNet)) continue;
                        $tmp = $item->vlanNet->name;
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'contentOptions' => ['style'=>'width: 100px;','data-index'=>7],
                'filterOptions' => ['data-index'=>7],
                'headerOptions' => ['data-index'=>7],
                'filter' => CVlanNet::find()->select(['name','id'])->indexBy('id')->column(),
            ],
            [
                'attribute' => 'dns_name',
                'label' => 'DNS-имена',
                'format' => 'html',
                'value' => function($model) {
                    if (!isset($model->nnEquipmentVlans)) return null;
                    $res = '';
                    foreach ($model->nnEquipmentVlans as $item) {
                        if (!isset($item->dnsName)) continue;
                        foreach ($item->dnsName as $itemDns) {
                            $tmp = $itemDns->dns_name;
                            $res .= "<br>$tmp";
                        }
                    }
                    return ltrim($res, "<br>");
                },
                'contentOptions' => ['nowrap'=>true,'data-index'=>8],
                'filterOptions' => ['data-index'=>8],
                'headerOptions' => ['data-index'=>8],
            ],
            [
                'attribute' => 'count_processors',
                'label' => 'Процессоров',
                'contentOptions' => ['data-index'=>9],
                'filterOptions' => ['data-index'=>9],
                'headerOptions' => ['data-index'=>9],
            ],
            [
                'attribute' => 'count_cores',
                'label' => 'Ядер CPU',
                'contentOptions' => ['data-index'=>10],
                'filterOptions' => ['data-index'=>10],
                'headerOptions' => ['data-index'=>10],
            ],
            [
                'attribute' => 'amount_ram',
                'label' => 'ОЗУ, Mb',
                'contentOptions' => ['data-index'=>11],
                'filterOptions' => ['data-index'=>11],
                'headerOptions' => ['data-index'=>11],
            ],
            [
                'attribute' => 'volume_hdd', // прямое поле не используем, а используем связанные
                'label' => 'HDD, Mb',
                'format' => 'html',
                'value' => function($model) {
                    if (!isset($model->cVolumeHdds)) return null;
                    $res = '';
                    foreach ($model->cVolumeHdds as $item) {
                        $tmp = $item->size;
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'contentOptions' => ['data-index'=>12],
                'filterOptions' => ['data-index'=>12],
                'headerOptions' => ['data-index'=>12],
            ],
            [
                'attribute' => 'description',
                'header' => 'Описание', // убираем сортировку
                'contentOptions' => ['data-index'=>13],
                'filterOptions' => ['data-index'=>13],
                'headerOptions' => ['data-index'=>13],
            ],
            [
                'attribute' => 'organization',
                'value' => 'organization0.name',
                'filter' => SOrganization::find()->select(['name','id'])->indexBy('id')->column(),
                'contentOptions' => ['data-index'=>14],
                'filterOptions' => ['data-index'=>14],
                'headerOptions' => ['data-index'=>14],
            ],
            [
                'attribute' => 'response_persons',
                'label' => 'Ответственные',
                'format' => 'html',
                'value' => function($model) {
                    if (!isset($model->sResponseEquipments)) return null;
                    $res = '';
                    foreach ($model->sResponseEquipments as $item) {
                        if (!isset($item->responsePerson)) continue;
                        $tmp = $item->responsePerson->fullName;
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'contentOptions' => ['nowrap'=>true,'data-index'=>15],
                'filterOptions' => ['data-index'=>15],
                'headerOptions' => ['data-index'=>15],
            ],
            [
                'attribute' => 'soft_installed',
                'label' => 'Программное обеспечение',
                'format' => 'html',
                'value' => function($model) {
                    if (!isset($model->rSoftwareInstalleds)) return null;
                    $res = '';
                    foreach ($model->rSoftwareInstalleds as $item) {
                        if (!isset($item->software0)) continue;
                        $tmp = $item->software0->name.' '.$item->software0->version;
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'filter' => RSoftware::find()->select(['concat(name," ",version)as name','id'])->indexBy('id')->column(),
                'contentOptions' => ['data-index'=>16],
                'filterOptions' => ['data-index'=>16],
                'headerOptions' => ['data-index'=>16],
            ],
            [
                'attribute' => 'information_systems',
                'label' => 'Информационные системы',
                'format' => 'html',
                'value' => function($model) {
                    /* @var REquipment $model */
                    if (empty($model->nnEquipmentInfosysContours)) return null;
                    $res = '';
                    foreach ($model->getNnEquipmentInfosysContours()->indexBy('information_system')->all() as $item) {
                        $tmp = $item->informationSystemModel->name_short;
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'filter' => \app\models\registry\RInformationSystem::find()->select(['name_short','id'])->indexBy('id')->orderBy("name_short")->column(),
                'contentOptions' => ['data-index'=>24],
                'filterOptions' => ['data-index'=>24],
                'headerOptions' => ['data-index'=>24],
            ],
            [
                'attribute' => 'information_system_contours',
                'label' => 'Контур',
                'format' => 'html',
                'value' => function($model) {
                    /* @var REquipment $model */
                    if (empty($model->nnEquipmentInfosysContours)) return null;
                    $res = '';
                    foreach ($model->getNnEquipmentInfosysContours()->indexBy('contour')->all() as $item) {
                        $tmp = $item->contourModel->name;
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'filter' => \app\models\reference\SContourInformationSystem::find()->select(['name','id'])->indexBy('id')->orderBy("name")->column(),
                'contentOptions' => ['data-index'=>25],
                'filterOptions' => ['data-index'=>25],
                'headerOptions' => ['data-index'=>25],
            ],
            [
                'attribute' => 'access_kspd',
                'value' => $yesNo('access_kspd'),
                'filter' => ['Нет','Есть'],
                'contentOptions' => ['data-index'=>17],
                'filterOptions' => ['data-index'=>17],
                'headerOptions' => ['data-index'=>17],
            ],
            [
                'attribute' => 'access_internet',
                'value' => $yesNo('access_internet'),
                'filter' => ['Нет','Есть'],
                'contentOptions' => ['data-index'=>18],
                'filterOptions' => ['data-index'=>18],
                'headerOptions' => ['data-index'=>18],
            ],
            [
                'attribute' => 'connect_arcsight',
                'value' => $yesNo('connect_arcsight'),
                'filter' => ['Нет','Есть'],
                'contentOptions' => ['data-index'=>19],
                'filterOptions' => ['data-index'=>19],
                'headerOptions' => ['data-index'=>19],
            ],
            [
                'attribute' => 'claims',
                'label' => 'Заявки',
                'format' => 'html',
                'value' => function($model) {
                    if (!isset($model->nnEquipmentClaims)) return null;
                    $res = '';
                    foreach ($model->nnEquipmentClaims as $item) {
                        if (!isset($item->claim0)) continue;
                        $tmp = $item->claim0->name;
                        $res .= "<br>$tmp";
                    }
                    return ltrim($res, "<br>");
                },
                'filter' => SClaim::find()->select(['name','id'])->indexBy('id')->column(),
                'contentOptions' => ['data-index'=>20],
                'filterOptions' => ['data-index'=>20],
                'headerOptions' => ['data-index'=>20],
            ],
            [
                'attribute' => 'parent',
                // 'label'=>'Альтернативный загловок столбца',
                'value' => 'parent0.name',
                'filter' => ArrayHelper::map($searchModel->find()->select('id, name')->asArray()->All(), 'id', 'name'),
                'contentOptions' => ['data-index'=>21],
                'filterOptions' => ['data-index'=>21],
                'headerOptions' => ['data-index'=>21],
            ],
            [
                'attribute' => 'backuping',
                'format' => 'html',
                'label' => 'Бекапирование',
                'value' => function($model){
                    return $model->backuping ? 'Есть' : 'Нет' ;
                },
                'filter' => ['Нет','Есть'],
                'contentOptions' => ['data-index'=>22],
                'filterOptions' => ['data-index'=>22],
                'headerOptions' => ['data-index'=>22],
            ],
            // 'manufacturer',
            // 'serial_number',
            // 'inventory_number',
            // 'date_commission',
            // 'date_decomission',
            // 'placement',
            // 'access_remote',
            // 'icing_internet',
            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'contentOptions' => ['style'=>'text-align: center;','data-index'=>23],
                'filterOptions' => ['data-index'=>23],
                'headerOptions' => ['data-index'=>23],
                // используем urlCreator, поскольку ip оборудования вытаскивается через $model->eq_ip_helper
                // 'urlCreator'=>function($action, $model, $key, $index) {
                //     return [$action, 'id' => $model->eq_ip_helper];
                // },
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('', $url, [
                            'class' => 'glyphicon glyphicon-trash showdelpopup',
                            'title' => 'Удалить',
                            'data-method' => 'post'
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>