<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\registry\RInformationSystem */

$this->title = 'Добавление информационной системы';
$this->params['breadcrumbs'][] = ['label' => 'Информационные системы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rinformation-system-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'showModalSwitcher' => $showModalSwitcher,
        'switcherId' => $switcherId,
        'isCreateScena' => true,
    ]) ?>

</div>
