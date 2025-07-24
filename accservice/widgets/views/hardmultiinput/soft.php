<?php

use app\widgets\HardMultiInput;
use kartik\helpers\Html;
use kartik\select2\Select2;
use yii\web\View;

?>
<div class="row multi-input <?=$relationModelName?>">
    <div class="col-md-12">
        <?php if (!is_array($fieldsNames)) $fieldsNames = array($fieldsNames); ?>
        <?php $firstIteration = true; ?>
        <?php foreach ($relationRows as $key => $relationModel): ?>
            <?php $inputUID = HardMultiInput::genInputUID(); ?>
            <?php if ( $firstIteration ): ?>
                <div class="row">
                    <div class="col-md-1"></div>
                    <?php foreach ($fieldsNames as $fieldName): ?>
                        <?php list($fN,$tN) = explode('.', $fieldName); ?>
                        <?php $tempGridIndex = HardMultiInput::genGrid([5,1,3,2,0]) ?>
                        <div class="col-md-<?= $tempGridIndex ?>">
                        <?php if ( $tempGridIndex ): ?>
                            <?= Html::label(
                                is_object($relationModel) ? $relationModel->getAttributeLabel($fN) : 'TEST',
                                null,
                                ['option' => 'value']
                            ); ?>
                        <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
            <div class="row multi-field-input <?= $firstIteration ? 'hidden-template-field' : '' ?>" style="<?= $firstIteration ? 'display:none;' : '' ?>">
                <div class="col-md-1">
                    <?= Html::button($buttonRemove, ['class' => 'btn btn-default btn-danger multi-field-remove '.$rmvBtnAddClass]); ?>
                </div>
                <?php foreach ($fieldsNames as $fieldName): ?>
                    <?php list($fN,$tN) = explode('.', $fieldName); ?>
                    <div class="col-md-<?= HardMultiInput::genGrid([5,1,3,2,0]) ?>">
                        <?php if ( $tN === HardMultiInput::TYPE_HARD_SELECT ): ?>
                            <?= Select2::widget([
                                'name' => "{$relationModelName}["
                                .(function()use($inputUID,$firstIteration){return $firstIteration ? '{{uid}}' : $inputUID;})()
                                ."][{$fN}]",
                                'value' => isset($relationModel->$fN) ? $relationModel->$fN : null,
                                'data' => $selectsData[$fN],
                                'options' => array_merge([ 'class' => 'form-control input-sm', 'disabled' => $firstIteration ? true : false ], isset($selectsOptions[$fN]) ? $selectsOptions[$fN] : []),
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]); ?>
                        <?php elseif( $tN === HardMultiInput::TYPE_SELECT ): ?>
                            <?= Html::dropDownList(
                                "{$relationModelName}["
                                .(function()use($inputUID,$firstIteration){return $firstIteration ? '{{uid}}' : $inputUID;})()
                                ."][{$fN}]", isset($relationModel->$fN) ? $relationModel->$fN : null, $selectsData[$fN],
                                array_merge([ 'class' => 'form-control input-sm', 'disabled' => $firstIteration ? true : false ], isset($selectsOptions[$fN]) ? $selectsOptions[$fN] : [])
                            ); ?>
                        <?php elseif( $tN === HardMultiInput::TYPE_INPUT ): ?>
                            <?= Html::textInput(
                                "{$relationModelName}["
                                .(function()use($inputUID,$firstIteration){return $firstIteration ? '{{uid}}' : $inputUID;})()
                                ."][{$fN}]",

                                (function() use($relationModel, $fN) {
                                    if ($fN === 'date_commission') {
                                        return isset($relationModel->$fN) ? Yii::$app->formatter->asDate($relationModel->$fN) : null;
                                    }
                                    return isset($relationModel->$fN) ? $relationModel->$fN : null;
                                })(),

                                [ 'class' => 'form-control input-sm', 'disabled' => $firstIteration ? true : false ]
                            ); ?>
                        <?php elseif( $tN === 'hidden' ): ?>
                             <?= Html::hiddenInput(
                                "{$relationModelName}["
                                .(function()use($inputUID,$firstIteration){return $firstIteration ? '{{uid}}' : $inputUID;})()
                                ."][{$fN}]", isset($relationModel->$fN) ? $relationModel->$fN : null,
                                [ 'class' => 'form-control input-sm', 'disabled' => $firstIteration ? true : false ]
                            ); ?>
                        <?php endif; ?>
                        <p class="help-block help-block-error"></p>
                    </div>
                <?php endforeach ?>
            </div>
            <?php $firstIteration = false; ?>
        <?php endforeach ?>
        <?= Html::button($buttonAdd, ['class' => 'btn btn-default btn-success multi-field-add '.$addBtnAddClass]); ?>
    </div>
</div>
<?php
$this->registerJs("

    $(document).on('click', '.{$relationModelName} .multi-field-remove', function(event){
        $(this).parents('.row.multi-field-input').remove();
    });

    $('.".$relationModelName." .multi-field-add').click(function(event) {
        var RandUID = Math.random().toString(36).substr(2, 11);
        var cloned = $(this)
        .parents('.row.multi-input')
        .find('.row.hidden-template-field')
        .clone(true)
        .insertBefore($(this));
        cloned.removeClass('hidden-template-field')
        .css('display','block')
        .html(function(idx, html) {
            // html = html.replace('select2-container--disabled','select2-container--below');
            html = html.replace(/w\d+/g,RandUID);
            return html;
        })
        .find('select,input')
        .prop('disabled',false)
        .attr('name', function(index, attr){
            return attr.replace('{{uid}}', RandUID);
        });

        var date = new Date();
        var month = date.getMonth()+1;
        var dayDate = date.getDate();
        if (month < 10) month = '0' + month;
        if (dayDate < 10) dayDate = '0' + dayDate;
        cloned.find('input[name *= \'date_commission\']').val( dayDate +'.'+ month +'.'+ date.getFullYear());

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

    $(document).on('mouseover', '.{$relationModelName} input[name *= \'date_commission\']', function(e) {
        $(this).datepicker({dateFormat: 'dd.mm.yy'});
    });

", View::POS_READY);
