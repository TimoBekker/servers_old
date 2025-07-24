<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftware */

$this->title = 'Добавление дистрибутива';
$this->params['breadcrumbs'][] = ['label' => 'Дистрибутивы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rsoftware-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'showModalSwitcher' => $showModalSwitcher,
        'switcherId' => $switcherId,
        'isCreateScena' => true,
    ]) ?>

</div>
