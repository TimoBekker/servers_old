<?php

use app\widgets\SimpleMultiInput;
use kartik\helpers\Html;
use yii\web\View;
?>
<div class="row multi-input <?=$relationModelName?>">
    <div class="col-md-12">
        <?php if (!is_array($fieldsNames)) $fieldsNames = array($fieldsNames); ?>
        <?php $firstIteration = true; ?>
        <?php foreach ($relationRows as $key => $relationModel): ?>
            <?php $inputUID = SimpleMultiInput::genInputUID(); ?>
            <?php if ( $firstIteration ): ?>
                <div class="row">
                    <div class="col-md-1"></div>
                    <?php foreach ($fieldsNames as $fieldName): ?>
                        <?php list($fN,$tN) = explode('.', $fieldName); ?>
                        <div class="col-md-<?= SimpleMultiInput::genGrid($gridRealization, empty($gridRealization) ? count($fieldsNames) : 0) ?>">
                            <?= Html::label(
                                is_object($relationModel) ? $relationModel->getAttributeLabel($fN) : 'TEST',
                                null,
                                ['option' => 'value']
                            ); ?>
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
                    <div class="col-md-<?= SimpleMultiInput::genGrid($gridRealization, empty($gridRealization) ? count($fieldsNames) : 0) ?>">
                        <?php if ( $tN === SimpleMultiInput::TYPE_SELECT ): ?>
                            <?= Html::dropDownList(
                                "{$relationModelName}["
                                .(function()use($inputUID,$firstIteration){return $firstIteration ? '{{uid}}' : $inputUID;})()
                                ."][{$fN}]", isset($relationModel->$fN) ? $relationModel->$fN : null, $selectsData[$fN],
                                array_merge([ 'class' => 'form-control input-sm', 'disabled' => $firstIteration ? true : false ], isset($selectsOptions[$fN]) ? $selectsOptions[$fN] : [])
                            ); ?>
                        <?php elseif( $tN === SimpleMultiInput::TYPE_INPUT ): ?>
                            <?= Html::textInput(
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
    $('.".$relationModelName." .multi-field-remove').click(function(event) {
        $(this).parents('.row.multi-field-input').remove();
    });
    $('.".$relationModelName." .multi-field-add').click(function(event) {
        var RandUID = Math.random().toString(36).substr(2, 11);
        $(this)
        .parents('.row.multi-input')
        .find('.row.hidden-template-field')
        .clone(true)
        .insertBefore($(this))
        .removeClass('hidden-template-field')
        .css('display','block')
        .find('select,input')
        .prop('disabled',false)
        .attr('name', function(index, attr){
            return attr.replace('{{uid}}', RandUID);
        });
    });
", View::POS_READY);
