<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\registry\RContract;
use app\models\reference\SOrganization;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ctype-equipment-form">

    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'contract')->dropDownList($tempRes = ArrayHelper::map(RContract::find()->select('id, name')->asArray()->All(), 'id', 'name'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>
	    <?= $form->field($model, 'organization')->dropDownList($tempRes = ArrayHelper::map(SOrganization::find()->select('id, name')->asArray()->All(), 'id', 'name'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>
	    <?php // prime - тоже ссылка на организацию, поэтому второй раз не прописываем ?>
	    <?= $form->field($model, 'prime')->dropDownList($tempRes/* = ArrayHelper::map(SOrganization::find()->select('id, name')->asArray()->All(), 'id', 'name')*/, ['prompt'=>'Нет']) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>
