<?php

use yii\helpers\Html;
?>

<p>
<?php $form = \yii\widgets\ActiveForm::begin(
    [
        'action' => Yii::$app->urlManager->createUrl(['admin/log/delete']) ,
        'options' => [
            'id' => 'form-delete-log',
            'method' => 'post',
            'class'=> "bg-danger",
            'style' => 'padding: 10px'
        ]
    ]
); ?>

<?=
yii\jui\DatePicker::widget([
                'name' => 'to',
                'language' => 'ru-Ru',
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'placeholder'=>'по',
                    'class' => 'form-control',
                    'style' => 'width: 30%; float:right', // т.к. флоат право, то вначале идет ПО, а потом С
                ]
            ])
?>
<?=
yii\jui\DatePicker::widget([
                'name' => 'from',
                'language' => 'ru-Ru',
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'placeholder'=>'с',
                    'class' => 'form-control',
                    'style' => 'width: 30%; float:right',
                ]
            ])
?>
<?= Html::submitButton('Удалить логи за выбранный период', ['class' => 'btn btn-danger', 'data' => [
            'confirm' => 'Вы уверены в том, что хотите удалить логи за выбранный период?',
],]) ?>

<?php \yii\widgets\ActiveForm::end(); ?>
</p>