<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = 'Создать группу';
$this->params['breadcrumbs'][] = ['label' => 'Управление группами и правами', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
