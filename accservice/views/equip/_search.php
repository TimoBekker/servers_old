<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\CTypeEquipment;
use yii\helpers\ArrayHelper;
use app\models\reference\SOrganization;
use app\models\reference\SVariantAccessRemote;
/* @var $this yii\web\View */
/* @var $model app\models\registry\REquipmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="requipment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php //= $form->field($model, 'id') ?>

    <?php //= $form->field($model, 'name') ?>

    <?= $form->field($model, 'description') ?>

    <?php // так как мы переделали поиск тype по строке, поэтому формируем список как name value=name ?>
    <?php //= $form->field($model, 'type')->dropDownList($tempRes = ArrayHelper::map(CTypeEquipment::find()->select('id, name')->asArray()->All(), 'name', 'name'), ['prompt'=>'Нет выбрано']) ?>

    <?= $form->field($model, 'manufacturer') ?>

    <?php echo $form->field($model, 'serial_number') ?>

    <?php echo $form->field($model, 'inventory_number') ?>

    <?php /* =  $form->field($model, 'date_commission')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru-Ru',
        'dateFormat' => 'yyyy-MM-dd',
        // 'empty' => 'sdf',
        'options' => [
            'class' => 'form-control',
        ]
    ])*/ ?>

    <?= Html::label('Дата ввода в эксплуатацию', 'date_commission_from')?>
    <div class="row">
        <div class="col-lg-6">
            <?=
            yii\jui\DatePicker::widget([
                'name' => 'date_commission_from',
                'value' => \Yii::$app->request->get('date_commission_from'),
                'attribute'=>'date_commission',
                'language' => 'ru-Ru',
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'placeholder'=>'с',
                    'class' => 'form-control',
                ]
            ])
            ?>
        </div>
        <div class="col-lg-6">
            <?=
            yii\jui\DatePicker::widget([
                'name' => 'date_commission_to',
                'value' => \Yii::$app->request->get('date_commission_to'),
                'attribute'=>'date_commission',
                'language' => 'ru-Ru',
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'placeholder'=>'по',
                    'class' => 'form-control',
                ]
            ])
            ?>
        </div>
    </div>
    <br>

    <?php /* = $form->field($model, 'date_decomission')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru',
        'dateFormat' => 'yyyy-MM-dd',
        'options' => [
            'class' => 'form-control',
        ]
    ]) */?>

    <?= Html::label('Дата вывода из эксплуатации', 'date_decomission_from')?>
    <div class="row">
        <div class="col-lg-6">
            <?=
            yii\jui\DatePicker::widget([
                'name' => 'date_decomission_from',
                'value' => \Yii::$app->request->get('date_decomission_from'),
                'attribute'=>'date_decomission',
                'language' => 'ru-Ru',
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'placeholder'=>'с',
                    'class' => 'form-control',
                ]
            ])
            ?>
        </div>
        <div class="col-lg-6">
            <?=
            yii\jui\DatePicker::widget([
                'name' => 'date_decomission_to',
                'value' => \Yii::$app->request->get('date_decomission_to'),
                'attribute'=>'date_decomission',
                'language' => 'ru-Ru',
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'placeholder'=>'по',
                    'class' => 'form-control',
                ]
            ])
            ?>
        </div>
    </div>
    <br>

    <?= $form->field($model, 'placement')->dropDownList($tempRes = ArrayHelper::map(Yii::$app->db->createCommand(
        'select id, concat(region, ", г. ", city, ", ул. ", street, " - ", house, ", каб. ", office, ", шк. ", locker, ", п. ", shelf) as place
           from s_placement'
    )->queryAll(), 'id', 'place'), ['prompt'=>'Не выбрано']) ?>

    <?= $form->field($model, 'organization')->dropDownList($tempRes = ArrayHelper::map(SOrganization::find()->select('id, name')->asArray()->All(), 'id', 'name'), ['prompt'=>'Нет выбрано']) ?>

    <?php // echo $form->field($model, 'count_cores') ?>

    <?php // echo $form->field($model, 'amount_ram') ?>

    <?php // echo $form->field($model, 'volume_hdd') ?>

    <?php echo $form->field($model, 'access_kspd')->dropDownList(['0' => 'Нет', '1' => 'Есть'], ['prompt'=>'Нет выбрано']) ?>

    <?php echo $form->field($model, 'access_internet')->dropDownList(['0' => 'Нет', '1' => 'Есть'], ['prompt'=>'Нет выбрано']) ?>

    <?php echo $form->field($model, 'connect_arcsight')->dropDownList(['0' => 'Нет', '1' => 'Есть'], ['prompt'=>'Нет выбрано']) ?>

    <?= $form->field($model, 'access_remote')->dropDownList($tempRes = ArrayHelper::map(SVariantAccessRemote::find()->select('id, variant')->asArray()->All(), 'id', 'variant'), ['prompt'=>'Не выбрано']) ?>

    <?php echo $form->field($model, 'icing_internet') ?>

    <?php // в отличие от формы добавления/редактирования здесь не будет блока where, а также как с типами, так как мы переделали под возможност строки в фильтрах, то option value=name name ?>
    <?php /* = $form->field($model, 'parent')->dropDownList(
            $tempRes = ArrayHelper::map(
                $model::find()->select('id, name')->asArray()->All(),
                'name',
                'name'
            ),
            ['prompt'=>'Не выбрано']
        ) */ ?>

    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Очистить', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
