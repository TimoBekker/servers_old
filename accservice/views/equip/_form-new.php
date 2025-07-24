<?php

/* @var $this yii\web\View */;
// use Yii;
use app\models\reference\SContourInformationSystem;
use app\models\registry\RInformationSystem;
use app\models\User;
use app\models\reference\CStateEquipment;
use app\models\reference\CTypeEquipment;
use app\models\reference\CVariantResponsibility;
use app\models\reference\CVlanNet;
use app\models\reference\CVolumeHdd;
use app\models\reference\SClaim;
use app\models\reference\SOpenPort;
use app\models\reference\SOrganization;
use app\models\reference\SResponseEquipment;
use app\models\reference\SVariantAccessRemote;
use app\models\registry\RDocumentation;
use app\models\registry\REquipment;
use app\models\registry\RLegalDoc;
use app\models\registry\RPassword;
use app\models\registry\RSoftwareInstalled;
use app\models\relation\NnEquipmentClaim;
use app\models\relation\NnEquipmentInfosysContour;
use app\models\relation\NnEquipmentVlan;
use app\widgets\HardMultiInput;
use app\widgets\SimpleMultiInput;
use app\widgets\SimpleMultiInputDoc;
use app\widgets\SimpleMultiInputPass;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\helpers\Html;
use kartik\select2\Select2;
use kartik\typeahead\TypeaheadBasic;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\MaskedInput;

$model->scenario = 'for_the_form';
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Оборудование успешно добавлено' ?></h4>
            </div>
            <!-- <div class="modal-body">
            </div> -->
            <div class="modal-footer">
                <?= Html::a('Перейти', ['equip/view', 'id' => isset($switcherId) ? $switcherId : ''], ['class' => 'btn btn-primary'])?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Добавить новое</button>
            </div>
        </div>
    </div>
</div>

<?php
$refrashUrl = Yii::$app->urlManager->createUrl(['equip/create']);
$this->registerJs("
$('#myModal').on('hidden.bs.modal', function (e) {
    window.location.replace('{$refrashUrl}');
})
", $this::POS_READY);
if(!empty($showModalSwitcher))
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
/**/
?>

<div class="requipment-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form
            ->field($model, 'type')->dropDownList(
                ArrayHelper::map(CTypeEquipment::find()->select('id, name')->asArray()->All(), 'id', 'name'),
                ['class'=>'form-control input-sm']
            )->label('ТИП')
            ?>
            <?php
                $this->registerJs("
                    $('#requipment-type').change(function(e){
                        $('#vmware-name-row-block').addClass('hidden');
                        $('#placement-row-block,#manufacturer-row-block').removeClass('hidden');
                        if ($(this).val() == 1) {
                            $('#vmware-name-row-block').removeClass('hidden');
                            $('#placement-row-block,#manufacturer-row-block').addClass('hidden');
                        }
                    });
                ", \yii\web\View::POS_READY);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form
            ->field($model, 'name')
            ->textInput(['maxlength' => 255, 'class'=>'form-control input-sm'])
            ->label('HOSTNAME', ['style'=>'font-size:18px']);
            ?>
        </div>
    </div>
    <div id="vmware-name-row-block" class="row hidden">
        <div class="col-md-6">
            <?= $form
            ->field($model, 'vmware_name')
            ->textInput(['maxlength' => 255, 'class'=>'form-control input-sm'])
            ->label('VMWARE-ИМЯ', ['style'=>'font-size:18px']);
            ?>
        </div>
    </div>
    <?php /* Место размещения */ ?>
    <div id="placement-row-block">
        <?php echo $this->render('_form-new/_block_splacement', ['model' => $model, 'form' => $form]); ?>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form
            ->field($model, 'state')
            ->dropDownList(CStateEquipment::find()->select(['name', 'id'])->indexBy('id')->column(),
                ['class'=>'form-control input-sm']
            )
            ->label('СТАТУС');
            ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'parent')->widget(Select2::classname(), [
                'data' => $model->selectParentList,
                'language' => 'ru',
                'options' => ['placeholder' => 'Выберите родителя', 'class'=>'input-sm'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textArea(['maxlength' => 2048, 'class'=>'form-control input-sm', 'rows'=>6]) ?>
        </div>
    </div>

    <?php if ($model->showResponsePersonsInputInForm): ?>
        <div id="respnseeuquipments-row-block" class="row">
            <div class="col-md-12">
                <?php
                $responsiblePersons = '<div class="panel-body"><div class="row"><div class="col-md-6">'
                    .$form->field($model, 'organization')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map(SOrganization::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name'),
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Выберите элемент', 'class'=>'input-sm'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])
                    ."</div></div>"
                    ."<div class='row'><div class='col-md-12'>"
                    .$form->field($model, 'sResponseEquipments')->widget(HardMultiInput::classname(), [
                        'gridRealization'=>[5,6],
                        'emptyRelationModel' => new SResponseEquipment(),
                        'fieldsNames' => ['response_person.hardselect','responsibility.select'],
                        'selectsData' => [
                            'response_person' => ArrayHelper::map(
                                User::find()
                                    ->select(['user.id as id', 'concat(
                                                                    user.last_name, " ",
                                                                    user.first_name, " ",
                                                                    user.second_name, " (",
                                                                    so.name, " )"
                                                                ) as name'])
                                    ->join('INNER JOIN', 's_organization so', 'so.id = user.organization')
                                    ->orderBy('user.last_name, user.first_name, user.second_name')
                                    ->asArray()
                                    ->All(), 'id', 'name'
                            ),
                            'responsibility' => ArrayHelper::map(CVariantResponsibility::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name')
                        ],
                    ])->label(false)
                    ."</div></div>"
                    ."</div>";
                ?>
                <?= Html::panel(
                    ['heading' => '<strong>ОТВЕТСТВЕННЫЕ</strong>', 'body' => $responsiblePersons],
                    Html::TYPE_DEFAULT,
                    [],
                    'azure panel panel-'
                );?>
            </div>
        </div>
    <?php endif ?>

    <div class="row">
        <div class="col-md-12">
            <?php $ipAdresses = $this->render('_form-new/_block_ipadresses', ['model' => $model, 'form' => $form]); ?>
            <?php $ipAdresses = '<div class="panel-body">'.$ipAdresses.'</div>' ?>
            <?= Html::panel(
                ['heading' => '<strong>IP-АДРЕСА, DNS-ИМЕНА</strong>', 'body' => $ipAdresses],
                Html::TYPE_DEFAULT,
                [],
                'gray panel panel-'
            );?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $characteristicsMain = "
                <div class='row'>
                    <div class='col-md-3'>"
                    .$form->field($model, 'count_processors')->textInput(['class'=>'form-control input-sm'])->label('Процессоров')
                    ."</div>
                    <div class='col-md-3'>"
                    .$form->field($model, 'count_cores')->textInput(['class'=>'form-control input-sm'])->label('Ядер в каждом')
                    ."</div>
                </div>
                <div class='row'>
                    <div class='col-md-3'>"
                    .$form->field($model, 'amount_ram')
                        ->textInput([
                            'value' => (function() use($model) {
                                if ($model->amount_ram) {
                                    return $model->amount_ram % 1024
                                    ? $model->amount_ram / 1000
                                    : $model->amount_ram / 1024;
                                }
                                return null;
                            })(), // overload value
                            'class'=>'form-control input-sm',
                            'placeholder'=>'Объем в Гб',
                        ])
                        ->label('ОЗУ')
                    ."</div>
                </div>
                <div class='row'>
                    <div class='col-md-6'>"
                    .$form->field($model, 'cVolumeHdds')->widget(SimpleMultiInput::classname(), [
                        'hddsLayout' => true,
                        'emptyRelationModel' => new CVolumeHdd(),
                        'fieldsNames' => ['size.input', 'is_dynamic.select'],
                        'selectsData' => ['is_dynamic' => ['Статический','Динамический']],
                        'selectsOptions' => ['is_dynamic' => ['prompt'=>'Выберите значение']],
                        'buttonAdd' => '+',
                        'buttonRemove' => '-',
                        'addBtnAddClass' => 'add-btn-with-plus',
                        'gridRealization' => [5,6],
                    ])->label('HDD')
                    ."</div>
                </div>
            ";
            $characteristics = "$characteristicsMain";
            $characteristics = "<div class='panel-body'>$characteristics</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>ХАРАКТЕРИСТИКИ</strong>', 'body' => $characteristics],
                Html::TYPE_DEFAULT,
                [],
                'azure panel panel-'
            );?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $installedSoft = "
            <div class='row'>
                <div class='col-md-12'>"
                .$form->field($model, 'rSoftwareInstalleds')->widget(HardMultiInput::classname(), [
                'softLayout' => true,
                'emptyRelationModel' => new RSoftwareInstalled(),
                'fieldsNames' => ['software.hardselect','bitrate.select','description.input','date_commission.input','id.hidden'],
                'selectsData' => [
                    'software' => $model->indexedSoftware2,
                    'bitrate' => [/*''=>null,*/'32'=>'32','64'=>'64']
                ],
                'selectsOptions' => ['bitrate' => ['prompt'=>'']],
            ])->label(false)
                ."</div>
            </div>
            ";
            $installedSoft = "<div class='panel-body'>$installedSoft</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>ПРОГРАММНОЕ ОБЕСПЕЧЕНИЕ</strong>', 'body' => $installedSoft],
                Html::TYPE_DEFAULT,
                [],
                'gray panel panel-'
            );?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php
            $hasInformationSystems = "
            <div class='row'>
                <div class='col-md-12'>"
                .$form->field($model, 'nnEquipmentInfosysContours')->widget(HardMultiInput::classname(), [
                    'emptyRelationModel' => new NnEquipmentInfosysContour(),
                    'fieldsNames' => ['information_system.hardselect', 'contour.select'],
                    'selectsData' => [
                        'information_system' => RInformationSystem::find()->select(['name_short','id'])->orderBy('name_short')->indexBy('id')->column(),
                        'contour' => SContourInformationSystem::find()->select(['name', 'id'])->indexBy('id')->column()
                    ],
                    'gridRealization' => [6,5],
                ])->label(false)
                ."</div>
            </div>
            ";
            $hasInformationSystems = "<div class='panel-body'>$hasInformationSystems</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>ИНФОРМАЦИОННЫЕ СИСТЕМЫ</strong>', 'body' => $hasInformationSystems],
                Html::TYPE_DEFAULT,
                [],
                'gray panel panel-'
            );?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php
            $documentation = "
            <div class='row'>
                <div class='col-md-12'>"
                .$form->field($model, 'rDocumentations')->widget(SimpleMultiInputDoc::classname(), [
                        'emptyRelationModel' => new RDocumentation(),
                        'fieldsNames' => ['name.input', 'description.input'],
                    ])->label(false)
                ."</div>
            </div>
            ";
            $documentation = "<div class='panel-body'>$documentation</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>ДОКУМЕНТАЦИЯ</strong>', 'body' => $documentation],
                Html::TYPE_DEFAULT,
                [],
                'azure panel panel-'
            );?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $paramsInformationSecure = "
            <div class='row'>
                <div class='col-md-6'>"
                .$form->field($model, 'access_kspd')->checkbox()
                .$form->field($model, 'access_internet')->checkbox()
                .$form->field($model, 'connect_arcsight')->checkbox()
                .$form->field($model, 'access_remote')->dropDownList(ArrayHelper::map(Yii::$app->db->createCommand(
                        'select sv.id as id, concat(ct.name, ", ", sv.variant) as variant
                           from s_variant_access_remote sv, c_type_access_remote ct
                          where sv.type = ct.id'
                )->queryAll(), 'id', 'variant'), ['prompt'=>'Выберите элемент', 'class'=>'input-sm form-control'])
                ."</div>
            </div>
            <div class='row'>
                <div class='col-md-12'>"
                .$form->field($model, 'icing_internet')->textInput(['maxlength' => 512, 'class'=>'input-sm form-control'])
                ."</div>
            </div>
            <div class='row'>
                <div class='col-md-12'>"
                .$form->field($model, 'sOpenPorts')->widget(SimpleMultiInput::classname(), [
                        'emptyRelationModel' => new SOpenPort(),
                        'fieldsNames' => ['port_number.input', 'protocol.select', 'description.input'],
                        'selectsData' => ['protocol' => $model->indexedProtocols],
                        'selectsOptions' => ['protocol' => ['prompt'=>'Протокол: не выбран']],
                        'buttonAdd' => '+',
                        'buttonRemove' => '-',
                        'addBtnAddClass' => 'add-btn-with-plus',
                        'gridRealization' => [2,2,7],
                    ])->label('Прослушиваемые порты')
                ."</div>
            </div>
            ";
            $paramsInformationSecure = "<div class='panel-body'>$paramsInformationSecure</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>ПАРАМЕТРЫ ИНФОРМАЦИОННОЙ БЕЗОПАСНОСТИ</strong>', 'body' => $paramsInformationSecure],
                Html::TYPE_DEFAULT,
                [],
                'gray panel panel-'
            );?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $stateEquipment = '<div class="panel-body"><div class="row"><div class="col-md-6">'
                .$form->field($model, 'backuping')->checkbox()->label('Резервное копирование')
                .'</div><div class="col-md-6">'
                .$form->field($model, 'last_backuped')->widget(DateTimePicker::className(), [
                    'language' => 'ru',
                    'size' => 'sm',
                    // 'template' => '{button}{input}',
                    'pickButtonIcon' => 'glyphicon glyphicon-time',
                    // 'inline' => true,
                    // 'clientOptions' => [
                    // 'startView' => 1,
                    // 'minView' => 0,
                    // 'maxView' => 1,
                    // 'autoclose' => true,
                    // 'linkFormat' => 'HH:ii P', // if inline = true
                    // 'format' => 'HH:ii P', // if inline = false
                    // 'todayBtn' => true
                    // ]
                    'options' => ['value' => $model->last_backuped ? Yii::$app->formatter->asDate($model->last_backuped, "yyyy-MM-dd HH:mm") : ""],
                    'clientOptions' => [
                        'autoclose' => 'true',
                        'todayBtn' => true,
                        'format' => 'yyyy-mm-dd hh:ii',
                        // 'format' => 'dd.mm.yyyy hh:ii',
                    ]
                ])->label("Дата и время последнего бекапирования")
                .'</div><div class="col-md-6">'
                .$form->field($model, 'date_commission')->widget(\yii\jui\DatePicker::classname(), [
                    'language' => 'ru-Ru',
                    // 'dateFormat' => 'yyyy-MM-dd',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'class' => 'form-control input-sm',
                    ]
                ])
                .'</div><div class="col-md-6">'
                .$form->field($model, 'date_decomission')->widget(\yii\jui\DatePicker::classname(), [
                    'language' => 'ru',
                    // 'dateFormat' => 'yyyy-MM-dd',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'class' => 'form-control input-sm',
                    ]
                ])
                ."</div></div>"
                ."</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>СТАТУС ОБОРУДОВАНИЯ</strong>', 'body' => $stateEquipment],
                Html::TYPE_DEFAULT,
                [],
                'azure panel panel-'
            ); ?>
            <?php
            $this->registerJs("
                var date = new Date();
                var month = date.getMonth()+1;
                var dayDate = date.getDate();
                if (month < 10) month = '0' + month;
                if (dayDate < 10) dayDate = '0' + dayDate;
                $('#requipment-state').change(function(e){
                    if ( (($(this).val()) == 3 || ($(this).val()) == 4) && ($('#requipment-date_decomission').val() === '') ) {
                        $('#requipment-date_decomission')
                        .val( dayDate +'.'+ month +'.'+ date.getFullYear())
                    }
                });
            ", View::POS_READY);
            ?>
        </div>
    </div>
    <div id="manufacturer-row-block" class="row">
        <div class="col-md-12">
            <?php
            $infoManufacturer = '<div class="panel-body"><div class="row"><div class="col-md-6">'
                .$form->field($model, 'manufacturer')->widget(TypeaheadBasic::classname(), [
                    'data' => REquipment::find()->select('manufacturer')->where("manufacturer != ''")->groupBy('manufacturer')->asArray()->column(),
                    'options' => ['class'=>'input-sm'],
                    'pluginOptions' => ['highlight' => true],
                ])
                ."</div></div>"
                .'<div class="row"><div class="col-md-6">'
                .$form->field($model, 'model')->widget(TypeaheadBasic::classname(), [
                    'data' => (function(){
                        $data = REquipment::find()->select('model')->where("model != ''")->groupBy('model')->asArray()->column();
                        if (empty($data)) {
                            $data = ['IBM'];
                        }
                        return $data;
                    })(),
                    'options' => ['class'=>'input-sm'],
                    'pluginOptions' => ['highlight' => true],
                ])->label('Модель')
                .'</div></div>'
                .'<div class="row"><div class="col-md-6">'
                .$form->field($model, 'serial_number')->textInput(['maxlength' => 128])
                .'</div><div class="col-md-6">'
                .$form->field($model, 'inventory_number')->textInput(['maxlength' => 128])
                ."</div></div>"
                ."</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>ИНФОРМАЦИЯ О ПРОИЗВОДИТЕЛЕ</strong>', 'body' => $infoManufacturer],
                Html::TYPE_DEFAULT,
                [],
                'gray panel panel-'
            );?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            $svyazanZayavki = "
            <div class='row'>
                <div class='col-md-12'>"
                .$form->field($model, 'nnEquipmentClaims')->widget(HardMultiInput::classname(), [
                'emptyRelationModel' => new NnEquipmentClaim(),
                'fieldsNames' => ['claim.hardselect'],
                'selectsData' => ['claim' => ArrayHelper::map(
                    SClaim::find()
                        ->select(['s_claim.id', 'CONCAT(s_claim.name, " (",c_agreement.name,")") as name'])
                        ->joinWith('agreement0')
                        ->all(),'id','name'
                )],
                'gridRealization' => [5],
            ])->label(false)
                ."</div>
            </div>
            ";
            $svyazanZayavki = "<div class='panel-body'>$svyazanZayavki</div>";
            ?>
            <?= Html::panel(
                ['heading' => '<strong>СВЯЗАННЫЕ ЗАЯВКИ</strong>', 'body'=>$svyazanZayavki],
                Html::TYPE_DEFAULT,
                [],
                'azure panel panel-'
            );?>
        </div>
    </div>
    <?php if ($model->showPassInputInForm): ?>
        <div class="row">
            <div class="col-md-12">
                <?php
                $passwords = "
                <div class='row'>
                    <div class='col-md-12'>"
                    .$form->field($model, 'rPasswords')->widget(SimpleMultiInputPass::classname(), [
                            'emptyRelationModel' => new RPassword(),
                            'fieldsNames' => ['login.input', 'description.input', 'password.password', 'confirmation.password'],
                        ])->label(false)
                    ."</div>
                </div>
                ";
                $passwords = "<div class='panel-body'>$passwords</div>";
                ?>
                <?= Html::panel(
                    ['heading' => '<strong>ПАРОЛИ</strong>', 'body' => $passwords],
                    Html::TYPE_DEFAULT,
                    [],
                    'gray panel panel-'
                );?>
            </div>
        </div>
        <?= Html::hiddenInput('runpassedit', true); ?>
    <?php endif ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerCss("
    .add-btn {
        padding:6px 8px;
    }
    .add-btn-with-plus {
        padding:6px 10.5px;
    }
    #ui-datepicker-div{
        z-index:1000 !important;
    }
    .azure.panel .panel-heading, .azure.panel .panel-body {
        background:aliceblue;
    }
    .gray.panel .panel-heading, .gray.panel .panel-body {
        background:#f9f9f9;
    }
");

//$this->registerJs("
//    var tempDataValue = $('#requipment-last_backuped').val();
//    if ( tempDataValue ) {
//        var momentInstance = moment(tempDataValue);
//        $('#requipment-last_backuped').val(momentInstance.format('DD.MM.YYYY HH:mm'));
//    }
//");

$this->registerJsFile('@web/js/whatPropsChanged.js',['depends' => 'yii\web\YiiAsset' ]);