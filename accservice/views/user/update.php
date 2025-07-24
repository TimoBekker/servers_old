<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Редактирование ответственного: ' . ' ' . $model->last_name.' '.$model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Редактирование справочников', 'url' => ['refs/index']];
$this->params['breadcrumbs'][] = ['label' => 'Ответственные лица', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->last_name.' '.$model->first_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование ответственного';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
