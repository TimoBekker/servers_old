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

	    <?= $form->field($model, 'equipment')->dropDownList($temp = ArrayHelper::map(REquipment::find()->select('id, name')->asArray()->All(), 'id', 'name'), empty($temp) ? ['prompt'=>'Нет оборудования'] : []) ?>
	    <?= $form->field($model, 'port_number')->textInput(['maxlength' => 5]) ?>
	    <?= $form->field($model, 'protocol')->dropDownList(ArrayHelper::map(CProtocol::find()->select('id, name')->asArray()->All(), 'id', 'name')) ?>
	    <?= $form->field($model, 'description')->textArea(['maxlength' => 255]) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>
