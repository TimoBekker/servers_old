<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftwareInstalledSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rsoftware-installed-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'software') ?>

    <?= $form->field($model, 'equipment') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'date_commission') ?>

    <?php // echo $form->field($model, 'bitrate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
