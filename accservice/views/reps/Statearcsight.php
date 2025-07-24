<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RInformationSystem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cостояние по подключению к системе ArcSight';
$this->params['breadcrumbs'][] = 'Отчеты: Cостояние по подключению к системе ArcSight';
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
                    'attribute' => 'connect_arcsight',
                    'contentOptions' => ['style'=>'width: 40%'],
                    'label' => 'Состояние подключения к ArcSight',
                    'value' => function($model){
                        return $model->connect_arcsight ? 'Есть' : 'Нет';
                    },
                    'footer' => $dataProvider['sum_params']['count_arc'],
                ],
            ],
        ]); ?>
    <?php endforeach ?>

    <?php // var_dump($dataProvider) ?>
</div>
