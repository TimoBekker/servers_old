<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\registry\RContract */

$this->title = 'Добавление контракта';
$this->params['breadcrumbs'][] = ['label' => 'Контракты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rcontract-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'showModalSwitcher' => $showModalSwitcher,
        'switcherId' => $switcherId,
    ]) ?>

</div>
