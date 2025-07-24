<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RLegalDoc */

$this->title = 'Редактирование НПА: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'НПА', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="rlegal-doc-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
