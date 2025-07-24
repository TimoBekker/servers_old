<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\registry\REquipment;
use app\models\registry\RLegalDoc;
use app\models\reference\CVariantResponsibility;
use app\models\User;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ctype-equipment-form">

    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'equipment')->dropDownList($tempRes = ArrayHelper::map(REquipment::find()->select('id, name')->asArray()->All(), 'id', 'name'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>
	    <?= $form->field($model, 'responsibility')->dropDownList($tempRes = ArrayHelper::map(CVariantResponsibility::find()->select('id, name')->asArray()->All(), 'id', 'name'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>
	    <?php // далее используем DAO, чтобы в запросе concat() не экранировалась строка с пробелом ' ' ?>
	    <?= $form->field($model, 'response_person')->dropDownList($tempRes = ArrayHelper::map(Yii::$app->db->createCommand(
	    	'select `us`.`id`,
	    			concat(`us`.`first_name`, " ", `us`.`last_name`, " ( ", `sor`.`name`, " )" ) as `usname`
	    	   from user us, s_organization sor
	    	  where us.organization = sor.id'
	    )->queryAll(), 'id', 'usname'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>
	    <?= $form->field($model, 'legal_doc')->dropDownList($tempRes = ArrayHelper::map(RLegalDoc::find()->select('id, name')->asArray()->All(), 'id', 'name'), ['prompt'=>'Нет']) ?>


<?php //var_dump($tempRes) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>