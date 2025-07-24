<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\registry\RDocumentation;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ctype-equipment-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>
        <?= $form->field($model, 'description')->textArea(['maxlength' => 512]) ?>

        <?php
        // Документация
        $rdocModel = RDocumentation::findOne(
            [
                'claim' => null,
                'equipment' => null,
                'information_system' => null,
                'software' => null,
                'agreement' => $model->id,
            ]
        );
        $rdocModel = $rdocModel ?: new RDocumentation;
        ?>
        <?= $form->beginField($rdocModel, 'RDocumentation'); ?>
            <?= Html::label('Ссылка на файл с соглашением') ?>
            <div class="input-group">
                <?= Html::activeFileInput($rdocModel, 'url', ['class'=>'form-control']) ?>
                <?php if ($rdocModel->url): ?>
                    <span class="input-group-btn"></span>
                    <?= Html::a('Текущий: '.basename($rdocModel->url), "@web/{$rdocModel->url}", ['class'=>'form-control']) ?>
                    <?= Html::Input('hidden', 'currenturl', $rdocModel->url, []) ?>
                <?php endif; ?>
            </div>
        <?= $form->endField(); ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
