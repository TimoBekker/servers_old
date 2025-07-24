<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Редактирование справочников', 'url' => ['refs/index']];
$this->params['breadcrumbs'][] = ['label' => $model::$tableLabelName, 'url' => ['refs/index', 'refid' => $refid]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ctype-equipment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'refid' => $refid, 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'refid' => $refid, 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить этот элемент?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    echo $this->render('view/'.mb_strtolower($refid), [
            'refid' => $refid,
            'model' => $model,
        ]);
    ?>

</div>
