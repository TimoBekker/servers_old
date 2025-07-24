<?php

use app\components\utils\Utils;
use app\models\reference\SIpdnsTable;
use app\models\registry\RDocumentation;
use app\models\relation\NnEquipmentVlan;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var yii\web\View $this */
/* @var \app\models\registry\REquipment $model */
/* @var array $informationSystems */

$isVirtualServer = !strcasecmp($model->type0->name,"Виртуальный сервер");

$this->title = $isVirtualServer ? $model->vmware_name : ($model->name ?: "-");
$this->params['breadcrumbs'][] = ['label' => 'Оборудование', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<?php Modal::begin([
    'id' => 'information-modal',
    'header' => '<h4></h4>',
    // 'size' => Modal::SIZE_LARGE,
    'options' => [
        'data-backdrop'=>"static",
    ], ]); ?>
    <div id = "information-modal-body"></div>
<?php Modal::end(); ?>

<div class="requipment-view">
    <p>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger showdelpopup',
            'style' => 'float:right;margin-left:5px;',
            'data' => [
                // 'confirm' => 'Вы действительно хотите удалить это оборудование?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary',
            'style' => 'float:right;',
            ]) ?>
    </p>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if ( $isVirtualServer ): ?>
        <div>VMware-имя: <strong><?= $model->vmware_name ?: IT_NOT_DEF ?></strong></div>
        <div>hostname: <strong><?= $model->name ?: IT_NOT_DEF ?></strong></div>
    <?php else: ?>
        <div>место размещения: <strong><?= $model->placementLook ?: IT_NOT_DEF ?></strong></div>
    <?php endif ?>
    <div>статус: <span style="font-weight:bold;color:<?= $model->state == 1 ? 'green' : 'red' ; ?>"><?= $model->state0->name ?></span></div>
    <?php
        $this->registerJs("
            if ( $model->state == 3) {
                $('#main-info-bloc-content').css('color', 'grey');
            }
        ", View::POS_READY);
    ?>
    <hr>
    <div id="main-info-bloc-content">
        <div><strong>Тип:</strong> <a href=""><?= $model->type0->name ?></a></div>
        <div><strong>Родительское оборудование:</strong> <?= !is_null($model->parent0) ?
            Html::a($model->parent0->name, ['view', 'id' => $model->parent]) : IT_NOT_DEF ?></div>
        <div><strong>Описание:</strong> <div style="margin-left: 30px"><?= nl2br($model->description) ?: IT_NOT_DEF ?></div></div>

        <br>

        <?php
            // Ответственные за оборудование
            $responsePersons = '';
            foreach ($model->sResponseEquipments as $value) {
                $fio = "{$value->responsePerson->last_name} {$value->responsePerson->first_name} ({$value->responsePerson->organization0->name})";
                $responsePersons .= Html::a($fio, ['user/view', 'id' => $value->responsePerson->id]).' ';
                $responsePersons .= '<ul style="list-style-type:none">';
                $responsePersons .= '<li>'.$value->responsibility0->name.'</li>';
                $responsePersons .= $value->legalDoc
                    ?
                        '<li>'.Html::a($value->legalDoc->name, ['ldoc/view', 'id' => $value->legalDoc->id]).'</li>'
                    :
                        '';
                if ($value->responsePerson->email) {
                    $responsePersons .= '<li> email: '.$value->responsePerson->email.'</li>';
                }
                if ( $value->responsePerson->phone_work || $value->responsePerson->phone_cell) {
                    $responsePersonsRazdelitel = $value->responsePerson->phone_work ? ',' : '';
                    $responsePersons .= '<li> тел: '.$value->responsePerson->phone_work;
                    $responsePersons .= $value->responsePerson->phone_cell ? $responsePersonsRazdelitel.' '.$value->responsePerson->phone_cell.'</li>' : '</li>';
                }
                $responsePersons .= '</ul>';
            }

            // VLan ip and Днс имена
            $dnsNames = '';
            $query = SIpdnsTable::find()
                ->select('s_ipdns_table.id, s_ipdns_table.ip_address, s_ipdns_table.dns_name')
                ->join('INNER JOIN', 'nn_equipment_vlan nev', 'nev.ip_address = s_ipdns_table.ip_address')
                ->join('INNER JOIN', 'r_equipment e', 'e.id = nev.equipment')
                ->where(['e.id' => $model->id])
                ->asArray()
                /*->all()*/;
            $dnsNamesArr = [];
            foreach ($query->each() as $items) {
                $dnsNames .= $items['dns_name'].' -> '.long2ip($items['ip_address']).'<br/>';
                $dnsNamesArr[long2ip($items['ip_address'])][] = $items['dns_name'];
            }

            $arrVlanHtml = '';
            foreach ($model->nnEquipmentVlans as $k => $v) {
                $n = $k+1;
                $vlanNetName = !is_null($v->vlan_net) ? Html::a($v->vlanNet->name." ({$v->vlanNet->description})", '#', ['onclick'=>'
                        var mdl = $("#information-modal");
                        var text = mdl.find(".modal-header h4").text();
                        mdl.modal("show").find(".modal-header h4").text("'.$v->vlanNet->name." ({$v->vlanNet->description})".'");
                        $.post("'.Url::to(['equip/get-vlan-list']).'", { id : "'.$v->vlanNet->id.'"}, function(data) {
                            $("#information-modal-body").html(data);
                        });
                        return false;
                    ']) : 'нет';
                $arrVlanHtml .= "<div>{$n}.
                    IP: <strong>".long2ip($v->ip_address)."</strong><br>
                    <span style='margin-left:30px'> Маска: ".Utils::maskToBin($v->mask_subnet)."
                    (".long2ip($v->mask_subnet).") </span><br>
                    <span style='margin-left:30px'> VLAN: <strong>{$vlanNetName}</strong></span><br>
                </div>";
                if ( isset($dnsNamesArr[long2ip($v->ip_address)]) ) {
                    foreach ( $dnsNamesArr[long2ip($v->ip_address)] as $valueDnsName) {
                        // $arrVlanHtml .= "<div>DNS: $valueDnsName</div>";
                        $arrVlanHtml .= "<div style='margin-left:30px'>DNS: ".Html::a($valueDnsName, 'http://'.$valueDnsName,['target'=>'__blank'])."</div>";
                    }
                }
                $arrVlanHtml .= '<br>';
            }

            // Программное обеспечение
            $installsoft = '';
            foreach ($model->rSoftwareInstalleds as $value) {
                $softlink = $value->software0->name.' ';
                $softlink .= $value->software0->version.' ';
                $installsoft .= Html::a($softlink, ['softins/view', 'id' => $value->id]);
                $installsoft .= $value->bitrate ? "<div style='padding-left:30px'>{$value->bitrate} бит" : "<div style='padding-left:30px'>";
                // $installsoft .= $value->software0->description ? ", {$value->software0->description}</div>" : "</div>";
                $installsoft .= ", {$value->description}</div>";
                // $installsoft .= "<br>";
            }

            // Документация
            $documents = '';
            foreach ($model->rDocumentations as $value) {
                $documents .= '<div>'.Html::a($value->name.' <span class="glyphicon glyphicon-download-alt"></span>', $value->url, ['target'=>'__blank']);
                $documents .= '<br><span style="margin-left:30px">'.$value->description.'</span></div>';
            }

            // Открытые порты
            $openPorts = '';
            foreach ($model->sOpenPorts as $value) {
                $openPorts .=
                    '<span style="margin-left: 30px">'
                    .Html::a($value->port_number, '#', ['onclick'=>'
                        var mdl = $("#information-modal");
                        mdl.modal("show").find(".modal-header h4").text("Данный порт открыт также на");
                        $.post("'.Url::to(['equip/get-ports-list']).'", { port : "'.$value->port_number.'"}, function(data) {
                            $("#information-modal-body").html(data);
                        });
                        return false;
                    '])
                    .' / '
                    .$value->protocol0->name
                    .' <br><span style="margin-left: 60px">'
                    .$value->description
                    .'</span><br></span>';
            }



            // Связанные заявки
            $claims = '';
            foreach ($model->nnEquipmentClaims as $value) {
                $docMod = RDocumentation::find()->where(['claim' => $value->claim0->id])->One();
                $claimDoc = $docMod ? ' '.Html::a('', $docMod->url, ['class'=>'glyphicon glyphicon-download-alt']).'' : '';
                $claimAgree = $value->claim0->agreement0 ? Html::a($value->claim0->agreement0->name, ['refs/view', 'refid' => 'CAgreement','id' => $value->claim0->agreement0->id]) : IT_NOT_DEF;
                $claims .= "<div>".Html::a($value->claim0->name, ['refs/view', 'refid' => 'SClaim','id' => $value->claim0->id])." $claimDoc<br><span style=\"margin-left:30px\">Соглашение: $claimAgree</span></div>";
            }

            // пароли
            $passwords = '';
            $passModels = $model->rPasswords;
            if ( $passModels && $accessViewPassword ) {

                $passwords = '<table>';
                foreach ($passModels as $value) {
                    if ( $value->finale || $value->next ) continue; // не выводим "Уничтоженные" пароли
                    $getpass = "<span class='showpass' style='cursor:pointer;color:#337ab7;'>Показать пароль<span>";
                    $passwords .= sprintf('
                            <tr class="%s">
                                <td class="log-cell"><strong>%s</strong>&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;</td>
                                <td>%s</td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td nowrap class="pass-cell" style="font-family: Courier New; font-size: 14px"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td colspan="3">%s</td>
                            </tr>
                    ', $value->id, $value->login, $getpass, $value->description ?: str_replace('(', '(Описание ', IT_NOT_DEF));
                }
                $passwords .= '</table>';
                if ($passwords !== '<table></table>') {
                    // далее подключим скрипт отображения пароля
                    $search = <<< JS
                        $('.showpass').click(function(event) {
                            var button = $(this);
                            var passCell = button.parent().parent().find('.pass-cell');
                            if ( passCell.text() != ''){
                                passCell.text('');
                                button.text('Показать пароль');
                                button.css('background', '');
                                return;
                            }
                            var param1 = button.parent().parent().attr('class');
                            var param2 =  button.parent().parent().find('.log-cell strong').text();
                            $.post('', {param1: param1, param2: param2}, function(data, textStatus, xhr) {
                                if ( textStatus == 'success' ) {
                                    button.text('Скрыть пароль');
                                    if ( data )
                                        button.parent().parent().find('.pass-cell').text(data);
                                    else
                                        button.parent().parent().find('.pass-cell').text('Пустой пароль');
                                }
                                else
                                    alert('Ошибка! Обратитесь к разработчику!');
                            });
                        });
JS;
                    $this->registerJs($search, \yii\web\View::POS_READY);
                } else { $passwords = ''; } // если были пароли, но только те, которые выводить не следует

            } elseif ( !$accessViewPassword ) {

                $passwords = '<i style="color:#c55">Вы не имеете доступа к данному разделу системы</i>';

            }

            // дочернее оборудование
            $childrenEquipments = '';
            foreach ($model->rEquipments as $key => $value) {
                $valName = $value->name ?: IT_NOT_DEF;
                $childrenEquipments .= '<div>'
                .Html::a($valName, ['view', 'id' => $value->id])
                ."<br>VMware: ".Html::a($value->name ?: IT_NOT_DEF, ['view', 'id' => $value->id])."</div>";
                foreach ($value->nnEquipmentVlans as $k => $v) {
                    $n = $k+1;
                    $childrenEquipments .= "<span style='margin-left:30px'>ip$n ".long2ip($v->ip_address)."</span><br>";
                }
                $childrenEquipments .= "<br>";
            }

            // жесткие диски
            $volumeHdds = '<ul style="list-style-type:none; padding:0;margin:0">';
            foreach ($model->cVolumeHdds as $key => $value) {
                $num = $key+1;
                $volumeHddSize = $value->size <= 1024 ? $value->size." Mb" : ( ($value->size % 1000) ? round($value->size/1024) : round($value->size/1000) )." Gb";
                if ( is_null($value->is_dynamic) ) {
                    $volumeHddType = '';
                } elseif ( $value->is_dynamic ) {
                    $volumeHddType = ', динамический';
                } else {
                    $volumeHddType = ', статический';
                }
                $volumeHdds .= "<li>HDD$num: $volumeHddSize$volumeHddType</li>";
            }
            if ($volumeHdds === '<ul>') {
                $volumeHdds = '';
            } else {
                $volumeHdds .= '</ul>';
            }
        ?>

        <div class="row">
            <div class="col-md-6 need-height">
                <div style="background: aliceblue; height: inherit; padding: 5px;">
                    <h5><strong>IP-АДРЕСА, DNS-ИМЕНА</strong></h5>
                    <div><?= $arrVlanHtml ? $arrVlanHtml : IT_NOT_DEF ?></div>
                </div>
            </div>
            <div class="col-md-6 need-height">
                <div style="background: #f9f9f9; height: inherit; padding: 5px;">
                    <h5><strong>ОТВЕТСТВЕННЫЕ</strong></h5>
                    <div>Организация-владелец:<br>
                        <?= !is_null($model->organization0)
                            ?
                                Html::a($model->organization0->name, [
                                        'refs/view',
                                        'refid'=>'SOrganization',
                                        'id' => $model->organization
                                    ], [
                                        'style'=>'margin-left:30px;display:inline-block'
                                    ]
                                )
                            :
                                '<span style="margin-left: 30px">'.IT_NOT_DEF.'</span>' ?>
                    </div>
                    <br>
                    <div><?= $responsePersons ? $responsePersons : IT_NOT_DEF ?></div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6 need-height">
                <div style="background: #f9f9f9; height: inherit; padding: 5px;">
                    <h5><strong>ХАРАКТЕРИСТИКИ</strong></h5>
                    <div>
                        <?php if (!$isVirtualServer): ?>
                            <div>Процессоров: <?= $model->count_processors ?></div>
                            <div>Ядер в каждом: <?= $model->count_cores ?></div>
                        <?php else: ?>
                            <div>Уровень VMware: <?= $model->count_processors ?> x <?= $model->count_cores ?></div>
                            <div>Итого виртуальных процессоров: <?= $model->count_processors * $model->count_cores ?></div>
                        <?php endif ?>
                        <br>
                        <div>
                            <?php // необходимо будет сделать UPDATE accservice_schema.r_equipment SET amount_ram = amount_ram * 100 WHERE id > 0; ?>
                            <?php // необходимо будет сделать UPDATE accservice_schema.r_equipment SET volume_hdd = volume_hdd * 5000 WHERE id > 0; ?>
                            ОЗУ: <?= $model->amount_ram
                            ?
                                // "$model->amount_ram Mb ( ~ ".round($model->amount_ram/1024)." Gb )"
                                (function()use($model){
                                    if ($model->amount_ram < 1024 ) {
                                        return "($model->amount_ram Mb)";
                                    } else {
                                        return round($model->amount_ram/1024)."Gb ($model->amount_ram Mb)";
                                    }
                                })()
                            :
                                IT_NOT_DEF ?>
                        </div>
                        <br>
                        <?= $volumeHdds ?: IT_NOT_DEF ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 need-height">
                <div style="background: aliceblue; height: inherit; padding: 5px;">
                    <h5><strong>ПРОГРАММНОЕ ОБЕСПЕЧЕНИЕ</strong></h5>
                    <div><?= $installsoft ? $installsoft : IT_NOT_DEF ?></div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <?= $this->render("_view-new/_informationSystems", ["informationSystems" => $informationSystems]) ?>
            <div class="col-md-6 need-height">
                <div style="background: #f9f9f9; height: inherit; padding: 5px;">
                    <h5><strong>ДОКУМЕНТАЦИЯ</strong></h5>
                    <div><?= $documents ? $documents : IT_NOT_DEF ?></div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" id="three-block">
            <div class="col-md-6 need-height">
                <div class="row">
                    <div class="col-md-12" id="stat-oborud">
                        <div style="background: #f9f9f9; height: inherit; padding: 5px;">
                            <h5><strong>СТАТУС ОБОРУДОВАНИЯ</strong></h5>
                            <div>Резервное копирование: <span style="margin-left: 0px; color:<?= $model->backuping ? 'green' : '#c55' ?>"><?= $model->backuping ? 'есть' : 'нет' ?></span></div>
                            <br>
                            <div>Дата и время последнего бекапа:<br>
                                <span style="margin-left: 30px"><?= Yii::$app->formatter->asDateTime($model->last_backuped) ? : IT_NOT_DEF ?></span>
                            </div>
                            <div>Введено в эксплуатацию:<br>
                                <span style="margin-left: 30px"><?= Yii::$app->formatter->asDate($model->date_commission) ? : IT_NOT_DEF ?></span>
                            </div>
                            <div>Выведено из эксплуатации:<br>
                                <span style="margin-left: 30px"><?= Yii::$app->formatter->asDate($model->date_decomission) ? : IT_NOT_DEF ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12" id="svyaz-zayav">
                        <div style="background: aliceblue; height: inherit; padding: 5px;">
                            <h5><strong>СВЯЗАННЫЕ ЗАЯВКИ</strong></h5>
                            <div><?= $claims ?: IT_NOT_DEF ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 need-height">
                <div style="background: aliceblue; height: inherit; padding: 5px;">
                    <h5><strong>ПАРАМЕТРЫ ИНФОРМАЦИОННОЙ БЕЗОПАСНОСТИ</strong></h5>
                    <div>Доступ в КСПД: <span style="margin-left: 0px; color:<?= $model->access_kspd ? 'green' : '#c55' ?>"><?= $model->access_kspd ? 'есть' : 'нет' ?></span></div>
                    <div>Доступ в Интернет: <span style="margin-left: 0px; color:<?= $model->access_internet ? 'green' : '#c55' ?>"><?= $model->access_internet ? 'есть' : 'нет' ?></span></div>
                    <br>
                    <div>Соединение с Arcsight: <span style="margin-left: 0px; color:<?= $model->connect_arcsight ? 'green' : '#c55' ?>"><?= $model->connect_arcsight ? 'есть' : 'нет' ?></span></div>
                    <br>
                    <div>Удаленный доступ:<br>
                        <?= !is_null($model->accessRemote) ?
                            '<span style="margin-left: 30px">'.$model->accessRemote->variant.' '.
                            $model->accessRemote->type0->findOne($model->accessRemote->type)->name.'</span>'
                            : '<span style="margin-left: 30px">'.IT_NOT_DEF.'</span>' ?>
                    </div>
                    <div>Проброс в интернет:<br><span style="margin-left: 30px"><?= $model->icing_internet ?: IT_NOT_DEF ?></span></div>
                    <br>
                    <div>Прослушиваемые порты:<br>
                        <?= $openPorts ?: '<span style="margin-left: 30px">'.IT_NOT_DEF.'</span>' ?>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6 need-height">
            <?php if (!$isVirtualServer): ?>
                <div style="background: #f9f9f9; height: inherit; padding: 5px;">
                    <h5><strong>ИНФОРМАЦИЯ О ПРОИЗВОДИТЕЛЕ</strong></h5>
                    <div>Модель:<br><span style="margin-left: 30px"><?= $model->model ? Html::a($model->model, '#', ['onclick'=>'
                        var mdl = $("#information-modal");
                        var text = mdl.find(".modal-header h4").text();
                        mdl.modal("show").find(".modal-header h4").text("Оборудование данной модели");
                        $.post("'.Url::to(['equip/get-model-list']).'", { name : "'.$model->model.'"}, function(data) {
                            $("#information-modal-body").html(data);
                        });
                        return false;
                    ']) : IT_NOT_DEF; ?></span></div>
                    <div>Фирма производитель:<br><span style="margin-left: 30px"><?= $model->manufacturer ?: IT_NOT_DEF ?></span></div>
                    <br>
                    <div>Серийный номер:<br><span style="margin-left: 30px"><?= $model->serial_number ?: IT_NOT_DEF ?></span></div>
                    <div>Инвентарный номер:<br><span style="margin-left: 30px"><?= $model->inventory_number ?: IT_NOT_DEF ?></span></div>
                </div>
            <?php endif ?>
            </div>
            <div class="col-md-6 need-height">
                <div style="background: <?= $isVirtualServer ? '#f9f9f9' : 'aliceblue' ; ?>; height: inherit; padding: 5px;">
                    <h5><strong>ПАРОЛИ</strong></h5>
                    <div><?= $passwords ?: IT_NOT_DEF ?></div>
                </div>
            </div>
        </div>
        <br>
        <?php if (!$isVirtualServer): ?>
            <div class="row">
                <div class="col-md-6 need-height">
                    <div style="background: aliceblue; height: inherit; padding: 5px;">
                        <h5><strong>ДОЧЕРНЕЕ ОБОРУДОВАНИЕ</strong></h5>
                        <div style="margin-left: 30px"><?= $childrenEquipments ?: IT_NOT_DEF ?></div>
                    </div>
                </div>
                <div class="col-md-6 need-height">
                </div>
            </div>
        <?php endif ?>
    </div>
</div>
<?php
    $this->registerJs("
        var nh = $('.need-height');
        $.each(nh, function(index, val) {
            $(val).css('height', $(val).parent('.row')[0].clientHeight + 'px');
        });
    ", View::POS_READY);
    $this->registerJs("
        var threeBlockHeight = $('#three-block')[0].clientHeight;
        var braikerHeight = 20; // поправка на br
        var statOborud = $('#stat-oborud');
        var statOborudHeight = statOborud[0].clientHeight;
        var svyazZayav = $('#svyaz-zayav');
        var svyazZayavHeight = svyazZayav[0].clientHeight;
        if (statOborudHeight+svyazZayavHeight+braikerHeight < threeBlockHeight) {
            svyazZayav.css('height', threeBlockHeight - statOborudHeight - braikerHeight)
        }
    ", View::POS_READY);