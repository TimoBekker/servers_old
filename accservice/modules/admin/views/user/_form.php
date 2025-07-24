<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\SOrganization;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // = $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'last_name')->textInput() ?>

    <?= $form->field($model, 'first_name')->textInput() ?>

    <?= $form->field($model, 'second_name')->textInput() ?>

    <?= $form->field($model, 'position')->textInput() ?>

    <?= $form->field($model, 'phone_work')->textInput() // todo: сделать ui телефона в формате 8 (888) 888-88-88 ?>

    <?= $form->field($model, 'phone_cell')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'skype')->textInput() ?>

    <?= $form->field($model, 'additional')->textInput() ?>

	<?= $form->field($model, 'organization')->dropDownList(ArrayHelper::map(SOrganization::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name'), ['prompt'=>'Выберите элемент']) ?>

    <?php // = $form->field($model, 'leader')->textInput() ?>
    <?php /*= $form->field($model, 'leader')->dropDownList(
            $tempRes = ArrayHelper::map(
                $model::find()->select('id, last_name')->where('id != :id', ['id'=> is_null($model->id) ? false : $model->id])->asArray()->All(),
                'id',
                'last_name'
            ),
            ['prompt'=>'Нет']
        ) */?>
	<?php // делаем ту же чтуку, что и выше, но чтобы выводить ФИО работаем напрямую с DAO ?>
    <?= $form->field($model, 'leader')->dropDownList($tempRes = ArrayHelper::map(Yii::$app->db->createCommand(
    	'select `us`.`id`,
    			concat(`us`.`first_name`, " ", `us`.`last_name`, " ( ", `sor`.`name`, " )" ) as `usname`
    	   from user us, s_organization sor
    	  where us.organization = sor.id and us.id != :this_us_id
          order by `us`.`first_name`, `us`.`last_name`, `sor`.`name`'
    )
    ->bindValue(':this_us_id', is_null($model->id) ? false : $model->id) // исключаем самаого себя
    ->queryAll(), 'id', 'usname'), ['prompt'=>'Выберите элемент']) ?>

	<?php // todo: сделать еще зависимый выбор пользователя от от выбранной организации в предыдущем поле ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php // скрытое поле пароля со значение password, чтобы пароль не слетал при редактировании польз-я ?>
    <?= $form->field($model, 'password_hash')->hiddenInput(['value' => 'password'])->label('') ?>

    <?php ActiveForm::end(); ?>

</div>
