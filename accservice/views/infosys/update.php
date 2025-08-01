<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RInformationSystem */

$this->title = 'Редактирование информационной системы: ' . ' ' . $model->name_short;
$this->params['breadcrumbs'][] = ['label' => 'Информационные системы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_short, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="rinformation-system-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'isCreateScena' => false,
    ]) ?>

</div>
