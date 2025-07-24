<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\registry\REvent */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'События', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revent-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить это событие?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <h3><?= Html::encode("Основные данные:") ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'name',
            'type' => [
                // 'label'=>'альтернативный заголовок',
                'attribute' => 'type',
                'value' => $model->type0->name // в этом виде мы можем так использовать Релейшн
            ],
            'description' => [
                'attribute' => 'description',
                'value' => $model->description,
            ],
            'date_begin' => [
                'attribute' => 'date_begin',
                'format' => ['date', 'php:d.m.Y H:i'],
            ],
            'date_end' => [
                'attribute' => 'date_end',
                'format' => ['date', 'php:d.m.Y H:i'],
            ],
        ],
    ]) ?>


    <h3><?= Html::encode("Дополнительные данные:") ?></h3>
    <?php
        // var_dump($model->nnEventEquipments);
        $arrEquips = '';
        foreach ($model->nnEventEquipments as $k => $v) {
            $n = $k+1;
            // $arrEquips .= "{$n}. <a href='#'>{$v->equipment0->name}</a><br>";
            $arrEquips .= "{$n}. <a href='".
                \Yii::$app->UrlManager->createUrl(['equip/view', 'id'=>$v->equipment0->id]).
            "'>{$v->equipment0->name}</a><br>";
        }

        $arrInfosys = '';
        foreach ($model->nnEventIs as $k => $v) {
            $n = $k+1;
            // $arrInfosys .= "{$n}. <a href='#'>{$v->informationSystem->name}</a><br>";
            $arrInfosys .= "{$n}. <a href='".
                \Yii::$app->UrlManager->createUrl(['infosys/view', 'id'=>$v->informationSystem->id]).
            "'>{$v->informationSystem->name_short}</a><br>";
        }

    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Оборудование, участвующее в событии',
                'format' => 'html',
                'value'=> $arrEquips ? : null,
            ],
            [
                'label' => 'Информационные системы, участвующие в событии',
                'format' => 'html',
                'value'=> $arrInfosys ? : null,
            ],

        ],
    ]) ?>
</div>
