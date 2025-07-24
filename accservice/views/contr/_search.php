<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RContractSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rcontract-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'contract_subject') ?>

    <?= $form->field($model, 'requisite') ?>

    <?= $form->field($model, 'cost') ?>

    <?php // echo $form->field($model, 'date_complete') ?>

    <?php // echo $form->field($model, 'date_begin_warranty') ?>

    <?php // echo $form->field($model, 'date_end_warranty') ?>

    <?php // echo $form->field($model, 'url') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
