<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RInformationSystem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Используемые ресурсы';
$this->params['breadcrumbs'][] = 'Отчеты: Используемые ресурсы';
?>

<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // var_dump($arrDataProvider);exit; ?>

    <?php foreach ($arrDataProvider as $dataProvider): ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider['provider'],
            'showFooter' => true,
            'summary' => "",
            'caption' => $dataProvider['typename'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn', 'footer' => 'Суммарно:'],
                [
                    'attribute' => 'name',
                    'contentOptions' => ['style'=>'width: 40%'],
                    'format' => 'html',
                    'value' => function($model){
                        return Html::a($model->name, ['equip/view', 'id' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'count_cores',
                    'footer' => intval($dataProvider['sum_params']['sum_cc']),
                ],
                [
                    'attribute' => 'amount_ram',
                    'footer' => $dataProvider['sum_params']['sum_ar'] * 0.1 . ' Гб',
                    'value' => function($model){
                        return $model->amount_ram ? $model->amount_ram * 0.1 : null;
                    }
                ],
                [
                    'attribute' => 'volume_hdd',
                    'footer' => $dataProvider['sum_params']['sum_vh'] * 5 . ' Гб',
                    'value' => function($model){
                        return $model->volume_hdd ? $model->volume_hdd * 5 : null;
                    }
                ],
            ],
        ]); ?>
    <?php endforeach ?>

    <?php // var_dump($dataProvider) ?>
</div>
