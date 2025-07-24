<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RInformationSystemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rinformation-system-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name_short') ?>

    <?= $form->field($model, 'name_full') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'validation') ?>

    <?php // echo $form->field($model, 'protection') ?>

    <?php // echo $form->field($model, 'privacy') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
