<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftwareSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rsoftware-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'version') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'type_license') ?>

    <?php // echo $form->field($model, 'count_license') ?>

    <?php // echo $form->field($model, 'date_end_license') ?>

    <?php // echo $form->field($model, 'owner') ?>

    <?php // echo $form->field($model, 'method_license') ?>

    <?php // echo $form->field($model, 'developer') ?>

    <?php // echo $form->field($model, 'count_cores') ?>

    <?php // echo $form->field($model, 'amount_ram') ?>

    <?php // echo $form->field($model, 'volume_hdd') ?>

    <?php // echo $form->field($model, 'inventory_number') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
