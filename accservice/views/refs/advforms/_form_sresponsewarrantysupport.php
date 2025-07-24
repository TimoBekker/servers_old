<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\registry\RContract;
use app\models\User;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ctype-equipment-form">

    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'contract')->dropDownList($tempRes = ArrayHelper::map(RContract::find()->select('id, name')->asArray()->All(), 'id', 'name'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>
	    <?php // далее используем DAO, чтобы в запросе concat() не экранировалась строка с пробелом ' ' ?>
	    <?= $form->field($model, 'response_person')->dropDownList($tempRes = ArrayHelper::map(Yii::$app->db->createCommand(
	    	'select `us`.`id`,
	    			concat(`us`.`first_name`, " ", `us`.`last_name`, " ( ", `sor`.`name`, " )" ) as `usname`
	    	   from user us, s_organization sor
	    	  where us.organization = sor.id'
	    )->queryAll(), 'id', 'usname'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>
	    <?php //= $form->field($model, 'response_person')->dropDownList($tempRes = ArrayHelper::map(User::find()->select("id, concat(`first_name`, , `last_name`) as `first_name`")->asArray()->All(), 'id', 'first_name'), empty($tempRes) ? ['prompt'=>'Нет элементов для выбора'] : []) ?>


<?php //var_dump($tempRes) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>

</div>
