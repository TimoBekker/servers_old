<?php

use yii\helpers\Html;
use yii\grid\GridView;

use app\models\reference\CTypeLegalDoc;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RLegalDocSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'НПА';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rlegal-doc-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить НПА', ['create'], ['class' => 'btn btn-success']) ?>
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
                    return Html::a($model->name, ['ldoc/view', 'id' => $model->id]);
                },
            ],
            // 'type',
            [
                'attribute' => 'type',
                'value' => function($model){
                    return $model->type0->name;
                },
                'filter' => ArrayHelper::map(CTypeLegalDoc::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            // 'date',
            'date' => [
                'attribute' => 'date',
                'format' => 'date',
                'filter' => yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_from',
                    'value' => \Yii::$app->request->get('date_from'),
                    'attribute'=>'date',
                    'language' => 'ru-Ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'с',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:left',
                    ]
                ]).yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_to',
                    'value' => \Yii::$app->request->get('date_to'),
                    'attribute'=>'date',
                    'language' => 'ru-Ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'по',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:right',
                    ]
                ]),
                'contentOptions' => ['style'=>'text-align: center;'],
            ],
            'number' => [
                'attribute' => 'number',
                'value' => function($model){
                    return $model->number ?: null;
                }
            ],
            // 'regulation_subject',
            // 'status',
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
