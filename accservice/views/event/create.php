<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\registry\REvent */

$this->title = 'Добавление события';
$this->params['breadcrumbs'][] = ['label' => 'События', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revent-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
