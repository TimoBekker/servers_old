<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RInformationSystem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Используемые ресурсы на системе виртуализации';
$this->params['breadcrumbs'][] = 'Отчеты: Используемые ресурсы на системе виртуализации';
?>

<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // var_dump($arrDataProvider);exit; ?>

    <?php foreach ($arrDataProvider as $dataProvider): ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider['provider'],
            'showFooter' => true,
            'summary' => "",
            'caption' => $dataProvider['parent']->name,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'footer' => 'Суммарно:<hr>Максимум доступно:'
                ],
                [
                    'attribute' => 'name',
                    'contentOptions' => ['style'=>'width: 40%'],
                    'format' => 'html',
                    'value' => function($model){
                        return Html::a($model->name, ['equip/view', 'id' => $model->id]);
                    },
                    'footer' => '&nbsp;<hr>'
                ],
                [
                    'attribute' => 'count_cores',
                    'footer' => intval($dataProvider['sum_params']['sum_cc']).'<hr><span style="color:black">'.intval($dataProvider['parent']->count_cores).'<span>',
                    'footerOptions' => [
                        'style'=> $dataProvider['parent']->count_cores
                            ?
                                ($dataProvider['sum_params']['sum_cc'] * 100 / $dataProvider['parent']->count_cores < 80) ? 'color: green' : 'color: red'
                            :
                                'color: red'
                        ],
                ],
                [
                    'attribute' => 'amount_ram',
                    'footer' => intval($dataProvider['sum_params']['sum_ar'] ) . ' Мб'.'<hr><span style="color:black">'.intval($dataProvider['parent']->amount_ram ). ' Мб'.'<span>',
                    'value' => function($model){
                        return $model->amount_ram ? $model->amount_ram: null;
                    },
                    'footerOptions' => [
                        'style'=> $dataProvider['parent']->amount_ram
                            ?   // поскольку мы высчитываем просто разность проентов, умножать на 100 нам не надо, а 100 в формуле это просто совпадение
                                ($dataProvider['sum_params']['sum_ar'] * 100 / $dataProvider['parent']->amount_ram < 80) ? 'color: green' : 'color: red'
                            :
                                'color: red'
                        ],
                ],
                [
                    'attribute' => 'volume_hdd',
                    'footer' => intval($dataProvider['sum_params']['sum_vh'] / 1000). ' Гб'.'<hr><span style="color:black">'.intval($dataProvider['parent']->volume_hdd / 1000). ' Гб'.'<span>',
                    'value' => function($model){
                        return $model->volume_hdd ? $model->volume_hdd / 1000 : null;
                    },
                    'footerOptions' => [
                        'style'=> $dataProvider['parent']->volume_hdd
                            ?
                                ($dataProvider['sum_params']['sum_vh'] * 100 / $dataProvider['parent']->volume_hdd < 80) ? 'color: green' : 'color: red'
                            :
                                'color: red'
                        ],
                ],
            ],
        ]); ?>
    <?php endforeach ?>

    <?php // var_dump($dataProvider) ?>
</div>
