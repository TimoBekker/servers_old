<?php

use app\models\reference\CVlanNet;
use app\models\reference\SIpdnsTable;
use app\models\relation\NnEquipmentVlan;
use app\widgets\SimpleMultiInput;
use kartik\helpers\Html;
use kartik\select2\Select2;
use yii\web\View;
use yii\widgets\MaskedInputAsset;

$relationModels = $model->nnEquipmentVlans;
/*$relationModels = [
    // new NnEquipmentVlan(),
    // new NnEquipmentVlan([
    //         'ip_address' => '12321',
    //         'mask_subnet' => '21',
    //         'vlan_net' => '9',
    //     ]),
    // new NnEquipmentVlan(),
];*/
array_unshift($relationModels, new NnEquipmentVlan()); // для шаблонного пустого поля
$relationModelsInner = [];
foreach ($relationModels as $relationModel) {
    $relationModelsInner[] = SIpdnsTable::find()
        ->where(['ip_address' => $relationModel->ip_address])
        ->all();
}
/*$relationModelsInner = [
    // [], // пустой массив для пустой внешней модели
    // [new SIpdnsTable(['dns_name'=>'hulio.sdns.ru', 'ip_address' => '23']), new SIpdnsTable, ],
    // [new SIpdnsTable, new SIpdnsTable, new SIpdnsTable, ],
    // [new SIpdnsTable, ],
];*/
$dobabit = count($relationModels) - count($relationModelsInner);
for ($i = 0; $i < $dobabit; $i++) {
    $relationModelsInner[] = [];
}

$relationModelsInner = array_map(function($val){
    array_unshift($val, new SIpdnsTable());
    return $val;
}, $relationModelsInner);
?>

<div class="row multi-input NnEquipmentVlan">
    <div class="col-md-12">
        <?php $outerFirstIteration = true; ?>
        <?php foreach ($relationModels as $outerКey => $outerValue): ?>
            <?php $outerInputUID = SimpleMultiInput::genInputUID(); ?>
            <div class="row outer-field-row <?= $outerFirstIteration ? 'hidden-outer-template-field' : '' ?>" style="display:<?= $outerFirstIteration ? 'none' : 'block'; ?>">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-1">&nbsp;</div>
                        <div class="col-md-5"><?= Html::label('IP-адрес'); ?></div>
                        <div class="col-md-2"><?= Html::label('/ Маска подсети'); ?></div>
                        <div class="col-md-4"><?= Html::label('VLAN'); ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-1"><?= Html::button('Удалить', ['class' => 'btn btn-default btn-danger multi-field-remove rmv-btn']); ?></div>
                        <div class="col-md-5">
                            <?= Html::textInput(
                                "NnEquipmentVlan["
                                .(function()use($outerInputUID,$outerFirstIteration){return $outerFirstIteration ? '{{uid}}' : $outerInputUID;})()
                                ."][ip_address]",
                                $outerValue->ipAddressLookable,
                                [ 'class' => 'form-control input-sm ip-masked-input', 'disabled' => $outerFirstIteration ? true : false ,
                                'placeholder' => 'Ip в формате 10.0.12.123']
                            ); ?>
                        </div>
                        <div class="col-md-2">
                            <?= Html::textInput(
                                "NnEquipmentVlan["
                                .(function()use($outerInputUID,$outerFirstIteration){return $outerFirstIteration ? '{{uid}}' : $outerInputUID;})()
                                ."][mask_subnet]",
                                // $outerValue->maskSubnetDecimale,
                                $outerValue->maskSubnetLookable, // не захотели в виде /21
                                [ 'class' => 'form-control input-sm', 'disabled' => $outerFirstIteration ? true : false ,
                                'placeholder' => 'В формате 255.255.0.0']
                            ); ?>
                        </div>
                        <div class="col-md-4">
                            <?= Select2::widget([
                                'name' => "NnEquipmentVlan["
                                .(function()use($outerInputUID,$outerFirstIteration){return $outerFirstIteration ? '{{uid}}' : $outerInputUID;})()
                                ."][vlan_net]",
                                'value' => $outerValue->vlan_net,
                                'data' => CVlanNet::find()->select(['CONCAT(name," (",description,")") as name', 'id'])->orderBy('name')->indexBy('id')->column(),
                                'options' => ['placeholder' => 'Выберите элемент', 'class' => 'form-control input-sm', 'disabled' => $outerFirstIteration ? true : false ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row multi-input-inner SIpdnsTable">
                        <div class="col-md-6">&nbsp;</div>
                        <div class="col-md-6">
                            <?php $innerFirstIteration = true; ?>
                            <?php foreach ($relationModelsInner[$outerКey] as $innerКey => $innerValue): ?>
                                <?php $innerInputUID = SimpleMultiInput::genInputUID(); ?>
                                <?= $innerFirstIteration ? Html::label('Связанные DNS-имена') : ''; ?>
                                <div class="row inner-field-row <?= $innerFirstIteration ? 'hidden-template-field' : '' ?>" style="margin-bottom: 5px; display:<?= $innerFirstIteration ? 'none' : 'block'; ?>">
                                    <div class="col-md-1"><?= Html::button('-', ['class' => 'btn btn-default btn-danger multi-field-inner-remove rmv-btn-delete']); ?></div>
                                    <div class="col-md-11">
                                        <?= Html::textInput(
                                            "NnEquipmentVlan["
                                            .(function()use($outerInputUID,$outerFirstIteration){return $outerFirstIteration ? '{{uid}}' : $outerInputUID;})()
                                            ."][dns_names][]",
                                            // .(function()use($innerInputUID,$innerFirstIteration){return $innerFirstIteration ? '{{inruid}}' : $innerInputUID;})()
                                            // ."][dns_name]",
                                            $innerValue->dns_name,
                                            [ 'class' => 'form-control input-sm', 'disabled' => $innerFirstIteration ? true : false ]
                                        ); ?>
                                    </div>
                                </div>
                                <?php $innerFirstIteration = false; ?>
                            <?php endforeach ?>
                            <?= Html::button('+', ['class' => 'btn btn-default btn-success multi-field-inner-add add-btn-with-plus', 'style' => 'margin-bottom: 10px; display:block']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php $outerFirstIteration = false; ?>
        <?php endforeach ?>
        <?= Html::button('Добавить', ['class' => 'btn btn-default btn-success multi-field-add add-btn']); ?>
    </div>
</div>
<?php

MaskedInputAsset::register($this); // для использования маскед инпуд в скриптах js

$this->registerJs("

    $(document).on('click', '.NnEquipmentVlan .multi-field-remove', function(event){
        $(this).parents('.row.outer-field-row').remove();
    });

    $(document).on('click', '.NnEquipmentVlan .multi-field-inner-remove', function(event){
        $(this).parents('.row.inner-field-row').remove();
    });

    $(document).on('click', '.NnEquipmentVlan .multi-field-inner-add', function(event){
        var RandUID = Math.random().toString(36).substr(2, 11);

        var cloned = $(this)
        .parents('.row.multi-input-inner')
        .find('.row.hidden-template-field')
        .clone(true)
        .insertBefore($(this));

        cloned.removeClass('hidden-template-field')
        .css('display','block')
        .find('input')
        .prop('disabled',false);
        // .attr('name', function(index, attr){
        //     return attr.replace('{{inruid}}', RandUID);
        // });

    });

    $(document).on('click', '.NnEquipmentVlan .multi-field-add', function(event){
        var RandUID = Math.random().toString(36).substr(2, 11);

        var cloned = $(this)
        .parents('.row.multi-input')
        .find('.row.hidden-outer-template-field')
        .clone(true)
        .insertBefore($(this));

        cloned.removeClass('hidden-outer-template-field')
        .css('display','block')
        .html(function(idx, html) {
            html = html.replace(/w\d+/g,RandUID);
            return html;
        })
        .find('input,select')
        .attr('name', function(index, attr){
            return attr.replace('{{uid}}', RandUID);
        })
        .not('.inner-field-row input')
        .prop('disabled',false);

        // cloned.find('.ip-masked-input').inputmask('9{1,3}.9{1,3}.9{1,3}.9{1,3}');

        cloned.find('span.select2.select2-container.select2-container--krajee.select2-container--disabled')
        .remove();

        if (jQuery('#' + RandUID).data('select2')) {
            jQuery('#' + RandUID).select2('destroy');
        }

        // console.dir($('#' + RandUID).data());
        jQuery
        .when( jQuery('#' + RandUID).select2( window[$('#' + RandUID).data('krajeeSelect2')]  ) )
        .done(initS2Loading(RandUID, $('#' + RandUID).data('s2Options') ));

    });

    // $('.ip-masked-input').inputmask('9{1,3}.9{1,3}.9{1,3}.9{1,3}');

", View::POS_READY);