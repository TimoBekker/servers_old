<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="ctype-equipment-form">

    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'dns_name')->textInput(['maxlength' => 255]) ?>
	    <?= $form->field($model, 'ip_address')->textInput(
	    	[
	    		'maxlength' => 15,
	    		'value' => $model->ip_address ? long2ip($model->ip_address) : null,
	    		'placeholder'=>'Введите IP-адрес в формате 10.0.1.2',
	    		'pattern' => '((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)'
	    	]) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>