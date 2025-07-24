<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\CProtocol;
use app\models\registry\REquipment;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ctype-equipment-form">

    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'region')->textInput(['maxlength' => 255]) ?>
	    <?= $form->field($model, 'city')->textInput(['maxlength' => 128]) ?>
	    <?= $form->field($model, 'street')->textInput(['maxlength' => 255]) ?>
	    <?= $form->field($model, 'house')->textInput(['maxlength' => 128]) ?>
	    <?= $form->field($model, 'office')->textInput(['maxlength' => 128]) ?>
	    <?= $form->field($model, 'locker')->textInput(['maxlength' => 128]) ?>
	    <?= $form->field($model, 'shelf')->textInput(['maxlength' => 128]) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>
