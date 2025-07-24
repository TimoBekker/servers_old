<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RInformationSystem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Используемые лицензии';
$this->params['breadcrumbs'][] = 'Отчеты: Используемые лицензии';
?>

<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // var_dump($arrDataProvider[0]['provider']);exit; ?>

    <?php foreach ($arrDataProvider as $dataProvider): ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider['provider'],
            // 'showFooter' => true,
            'summary' => "",
            'caption' => $dataProvider['typename'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                	'attribute' => 'name',
                    'contentOptions' => ['style'=>'width: 40%'],
                    'format' => 'html',
                    'value' => function($model){
                        return $model->name ? Html::a($model->name, ['soft/view', 'id' => $model->id]) : null;
                    }
                ],
                [
                	'attribute' => 'version',
                    'contentOptions' => ['style'=>'width: 40%'],
                    'format' => 'html',
                    'value' => function($model){
                        return $model->version ? Html::a($model->version, ['soft/view', 'id' => $model->id]) : null;
                    }
                ],
                [
                	// 'attribute' => 'owner0.name', // можно и так, но так сортировка не подтянется, нужно будет в провайдере задавать
                	'attribute' => 'owner',
                	'label' => 'Орг-я - владелец',
                	'value' => 'owner0.name' // а вот так совсем недавно я только в доках вычитал
                ],
                [
                	'attribute' => 'method_license',
                	'label' => 'Способ лиценз-я',
                	'value' => 'methodLicense.name' // а вот так совсем недавно я только в доках вычитал
                ],
                [
                	'attribute' => 'count_license',
                	'label' => 'Кол-во доступных',
                	'value' => function($model){
                		return $model->count_license !== '' ? $model->count_license !== 0 ? $model->count_license : 'Бесконечное количество' : null;
                	}
                ],
                [
                	'attribute' => 'helperproperty',
                	'label' => 'Кол-во используемых',
                	'contentOptions' => ['class'=>'text-center'],
                ],
                [
                	'attribute' => 'date_end_license',
                	'format' => 'date',
                	'label' => 'Дата оконч. дейст.',
                	'contentOptions' => ['class'=>'text-center'],
                ],
            ],
        ]); ?>
    <?php endforeach ?>

    <?php // var_dump($dataProvider) ?>
</div>
