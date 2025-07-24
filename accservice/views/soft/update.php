<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftware */

$this->title = 'Редактирование дистрибутива: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Дистрибутивы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="rsoftware-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'isCreateScena' => false,
    ]) ?>

</div>
