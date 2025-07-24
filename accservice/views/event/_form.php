<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\CTypeEvent;
use yii\helpers\ArrayHelper;
use dosamigos\datetimepicker\DateTimePicker;
use yii\web\View;
use app\models\relation\NnEventEquipment;
use app\models\relation\NnEventIs;
use app\models\registry\REquipment;
use app\models\registry\RInformationSystem;

/* @var $this yii\web\View */
/* @var $model app\models\registry\REvent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="revent-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 512]) ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => 1024]) ?>

	<?=
    $form->field($model, 'type')
        ->dropDownList(
            ArrayHelper::map(
                CTypeEvent::find()->select('id, name')->asArray()->All(), 'id', 'name'),
                ['prompt'=>'Выберите элемент']
        )
    ?>

	<?= $form->field($model, 'date_begin')->widget(DateTimePicker::className(), [
	    'language' => 'ru',
	    // 'size' => 'ms',
	    'template' => '{button}{input}',
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
        'clientOptions' => [
            'autoclose' => 'true',
            'todayBtn' => true,
            'format' => 'dd.mm.yyyy hh:ii:ss',
        ]
    ]);?>

    <?= $form->field($model, 'date_end')->widget(DateTimePicker::className(), [
        'language' => 'ru',
        'template' => '{button}{input}',
        'pickButtonIcon' => 'glyphicon glyphicon-time',
        'clientOptions' => [
            'autoclose' => 'true',
            'todayBtn' => true,
            'format' => 'dd.mm.yyyy hh:ii:ss',
	    ]
	]);?>

    <?php // далее идут Реляционные поля многие-ко-многим ?>
    <?php // Связь с оборудованием: ?>
    <?php if ($arrEventEquips = $model->nnEventEquipments): ?>
        <?= Html::script('var equipmentMax = '.(count($arrEventEquips)-1), []); ?>
        <?php foreach ($model->nnEventEquipments as $keyv => $v): ?>
            <?php // var_dump($v->equipment0->id); ?>
            <?= $form->beginField(new NnEventEquipment, "[{$keyv}]equipment"); ?>
                <?php if ($keyv == 0): ?>
                    <label class="control-label" for="nneventequipment-equipment">Оборудование</label>
                <?php endif ?>
                <div class="input-group">
                    <?= Html::DropDownList(
                        // new NnEventEquipment,
                        "NnEventEquipment[{$keyv}][equipment]",
                        $v->equipment0->id,
                        ArrayHelper::map(
                            REquipment::find()->select('id, name')->asArray()->All(),'id','name'
                        ),
                        [
                            // 'id'=>'nneventequipment-'.$keyv.'-equipment',
	                        'prompt'=>'Нет',
                            'class'=>'form-control',
                            'style'=>'z-index:0'
                        ]
                    ) ?>
                    <span class="input-group-btn">
                        <button class="btn btn-success field-create-equipment" type="button">+</button>
                        <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                    </span>
                </div>
                <div class="help-block"></div>
            <?= $form->endField(); ?>

        <?php endforeach ?>
    <?php else: ?>
        <?= Html::script('var equipmentMax = 0', []); ?>
        <?= $form->beginField(new NnEventEquipment, '[0]equipment'); ?>
            <label class="control-label" for="nneventequipment-equipment">Оборудование</label>
            <div class="input-group">
                <?= Html::DropDownList(
                	'NnEventEquipment[0][equipment]',
                	false,
                	ArrayHelper::map(
                		REquipment::find()->select('id, name')->asArray()->All(), 'id', 'name'
                	),
        			[
        				'prompt'=>'Нет',
        				'class'=>'form-control',
        				'style'=>'z-index:0'
        			]
                )?>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-equipment" type="button">+</button>
                    <button class="btn btn-danger field-remove disabled" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>
    <?php endif ?>

    <?php // Связь с информационными системами: ?>
    <?php if ($arrEventInfosyst = $model->nnEventIs): ?>
        <?= Html::script('var infosystemMax = '.(count($arrEventInfosyst)-1), []); ?>
        <?php foreach ($model->nnEventIs as $keyv => $v): ?>
            <?php // var_dump($v->informationSystem->id); ?>
            <?= $form->beginField(new NnEventIs, "[{$keyv}]information_system"); ?>
                <?php if ($keyv == 0): ?>
                    <label class="control-label" for="nneventis-information_system">Информационные системы</label>
                <?php endif ?>
                <div class="input-group">
                    <?= Html::DropDownList(
                        // new NnEventIs,
                        "NnEventIs[{$keyv}][information_system]",
                        $v->informationSystem->id,
                        ArrayHelper::map(
                            RInformationSystem::find()->select('id, name_short')->asArray()->All(),'id','name_short'
                        ),
                        [
                            // 'id'=>'nneventis-'.$keyv.'-information_system',
	                        'prompt'=>'Нет',
                            'class'=>'form-control',
                            'style'=>'z-index:0'
                        ]
                    ) ?>
                    <span class="input-group-btn">
                        <button class="btn btn-success field-create-infosystem" type="button">+</button>
                        <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                    </span>
                </div>
                <div class="help-block"></div>
            <?= $form->endField(); ?>

        <?php endforeach ?>
    <?php else: ?>
        <?= Html::script('var infosystemMax = 0', []); ?>
        <?= $form->beginField(new NnEventIs, '[0]information_system'); ?>
            <label class="control-label" for="nneventis-information_system">Информационные системы</label>
            <div class="input-group">
                <?= Html::DropDownList(
                	'NnEventIs[0][information_system]',
                	false,
                	ArrayHelper::map(
                		RInformationSystem::find()->select('id, name_short')->asArray()->All(), 'id', 'name_short'
                	),
        			[
        				'prompt'=>'Нет',
        				'class'=>'form-control',
        				'style'=>'z-index:0'
        			]
                )?>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-infosystem" type="button">+</button>
                    <button class="btn btn-danger field-remove disabled" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>
    <?php endif ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
// далее скрипты на JS
$search = <<< JS
    // для динамически добавляемых полей используем делегирование через on
    jQuery(document).on('click', '.field-remove',function(event) {
        $(this).parent().parent().parent().remove();
        // alert('test');
        event.preventDefault();
    });
    jQuery(document).on('click', '.field-create-equipment',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        equipmentMax++;
        var ninputClass =
            'form-group field-nneventequipment-'+(equipmentMax)+'-equipment required';
        newel.attr('class', ninputClass);
        var ninputName = 'NnEventEquipment['+(equipmentMax)+'][equipment]';
        newel.find('select').attr('name', ninputName);
        var ninputId = 'nneventequipment-'+(equipmentMax)+'-equipment';
        newel.find('select').attr('id', ninputId);
        newel.find('button').removeClass('disabled');
    });
    jQuery(document).on('click', '.field-create-infosystem',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        infosystemMax++;
        var ninputClass =
            'form-group field-nneventis-'+(infosystemMax)+'-information_system required';
        newel.attr('class', ninputClass);
        var ninputName = 'NnEventIs['+(infosystemMax)+'][information_system]';
        newel.find('select').attr('name', ninputName);
        var ninputId = 'nneventis-'+(infosystemMax)+'-information_system';
        newel.find('select').attr('id', ninputId);
        newel.find('button').removeClass('disabled');
    });
JS;
    $this->registerJs($search, View::POS_READY);
    // where $position can be View::POS_READY (the default),
    // or View::POS_HEAD, View::POS_BEGIN, View::POS_END