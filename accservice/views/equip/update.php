<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\registry\REquipment */

$this->title = 'Редактирование оборудования: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Оборудование', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
$customScript = <<< SCRIPT
    $('#requipment-type').change();
SCRIPT;
$this->registerJs($customScript, \yii\web\View::POS_LOAD);
?>
<div class="requipment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <hr>

    <?= $this->render('_form-new', [
        'model' => $model,
        'isCreateScena' => false,
    ]) ?>

</div>
