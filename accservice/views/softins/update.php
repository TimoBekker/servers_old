<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftwareInstalled */

// $this->title = 'Редактирование установленной лицензии: ' . ' ' . $model->id;
$this->title = 'Редактирование уст. лицензии: ' . ' ' . $model->software0->name.' - '.$model->equipment0->name;
$this->params['breadcrumbs'][] = ['label' => 'Установленные лицензии', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->software0->name.' - '.$model->equipment0->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="rsoftware-installed-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
