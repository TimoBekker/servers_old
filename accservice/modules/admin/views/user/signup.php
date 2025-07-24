<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\SOrganization;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Задание аттрибутов авторизации для пользователя: '. $model->last_name.' '.$model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->last_name.' '.$model->first_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Задание аттрибутов авторизации';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<div class="user-form">

	    <?php $form = ActiveForm::begin(); ?>

	    <?= $form->field($model, 'username')->textInput() ?>

	    <?= $form->field($model, 'password_hash')->passwordInput(['value' => $model->password_hash ? 'password' : '', 'placeholder' => 'Введите пароль']) ?>

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

	    <?php ActiveForm::end(); ?>

	</div>

</div>