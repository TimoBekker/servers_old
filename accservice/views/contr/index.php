<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Контракты';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rcontract-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить контракт', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сбросить весь поиск', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'name' => [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->name, ['contr/view', 'id' => $model->id]);
                },
            ],
            'contract_subject' => [
                'attribute' => 'contract_subject',
                'value' => function($model){
                    return $model->contract_subject ?: null;
                }
            ],
            'requisite' => [
                'attribute' => 'requisite',
                'value' => function($model){
                    return $model->requisite ?: null;
                }
            ],
            // 'cost',
            [
                'attribute' => 'cost',
                'value' => function($model){
                    return !is_null($model->cost) ? $model->cost.' руб.' : null;
                },
            ],
            // 'date_complete',
            // 'date_begin_warranty',
            // 'date_end_warranty',
            // 'url:url',

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'contentOptions' => ['style'=>'text-align: center;'],
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('', $url, [
                            'class' => 'glyphicon glyphicon-trash showdelpopup',
                            'title' => 'Удалить',
                            'data-method' => 'post'
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
