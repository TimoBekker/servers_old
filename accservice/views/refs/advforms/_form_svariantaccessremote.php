<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\CTypeAccessRemote;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ctype-equipment-form">

    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'variant')->textInput(['maxlength' => 128]) ?>
	    <?= $form->field($model, 'type')->dropDownList($tempRes = ArrayHelper::map(CTypeAccessRemote::find()->select('id, name')->asArray()->All(), 'id', 'name'), empty($tempRes) ? ['prompt'=>'Нет вариантов для выбора'] : []) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>
