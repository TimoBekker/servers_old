<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ctype-equipment-form">

    <?php
        // динамически, в зависимости от модели, подготавливаем выводимые поля
        $attributes = $model->attributes;
        unset($attributes['id']);
        // var_dump(array_search('description', $attributes));exit;
        // var_dump($model->getValidators());exit;
        // var_dump($model->rules());exit;
        // var_dump($attributes);exit;

    ?>

    <?php $form = ActiveForm::begin(); ?>

	<?php foreach ($attributes as $keyAttr => $valueAttr): ?>
        <?php $method = $model->getAttributeRender($keyAttr)[0] ?>
        <?php $param2 = $model->getAttributeRender($keyAttr, $keyAttr === 'parent' ? (int)$model->id : null)[1] ?>
		<?php if ( $method === 'dropDownList' ): ?>
			<?= $form->field($model, $keyAttr)->$method($param2) ?>
		<?php else: ?>
			<?= $form->field($model, $keyAttr)->$method(['maxlength' => $param2]) ?>
		<?php endif ?>
	<?php endforeach ?>

<?php /*
	    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
	    <?= $form->field($model, 'description')->textarea() ?>
	    <?php //var_dump($form->field($model, 'description')) ?>
 */?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>
