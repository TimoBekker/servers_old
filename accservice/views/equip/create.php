<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\registry\REquipment */

$this->title = 'Добавление Оборудования';
$this->params['breadcrumbs'][] = ['label' => 'Оборудование', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$currentUserId = (int)Yii::$app->user->id;
$customScript = <<< SCRIPT
    $.when( $("#respnseeuquipments-row-block .multi-field-add").trigger('click') ).then(function() {
        $($("#respnseeuquipments-row-block .form-control.input-sm.select2-hidden-accessible")[2]).val('{$currentUserId}').change();
    });
    $('#requipment-type').val('1').change();

    var date = new Date();
    var month = date.getMonth()+1;
    var dayDate = date.getDate();
    if (month < 10) month = '0' + month;
    if (dayDate < 10) dayDate = '0' + dayDate;
    if ( $('#requipment-date_commission').val() === '' ) {
        $('#requipment-date_commission')
        .val( dayDate +'.'+ month +'.'+ date.getFullYear())
    }
SCRIPT;
$this->registerJs($customScript, \yii\web\View::POS_LOAD);
?>
<div class="requipment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <hr>

    <?= $this->render('_form-new', [
        'model' => $model,
        'showModalSwitcher' => $showModalSwitcher,
        'switcherId' => $switcherId,
        'isCreateScena' => true,
    ]) ?>

</div>
