<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\reference\CTypeEvent;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\REventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'События';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="revent-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить событие', ['create'], ['class' => 'btn btn-success']) ?>
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
                    return Html::a($model->name, ['event/view', 'id' => $model->id]);
                },
            ],
            'type'=>[
                'attribute' => 'type',
                // 'label'=>'Альтернативный загловок столбца',
                'value' => function($model){
                    return CTypeEvent::findOne((int)$model->type)->name;
                },
                'filter' => ArrayHelper::map(CTypeEvent::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            'description' => [
                'attribute' => 'description',
                'format' => 'html',
                'value' => function($model){
                    return $model->description;
                },
            ],
            // todo: сделать фильтр с.. - по.. для дат
            // 'date_begin',
            [
                'format' => ['date', 'php:d.m.Y H:i'], // здесь так зададим, чтоб убрать сек.
                'attribute' => 'date_begin',
                'filter' => yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_begin_from',
                    'value' => \Yii::$app->request->get('date_begin_from'),
                    'attribute'=>'date_begin',
                    'language' => 'ru-Ru',
                    // 'dateFormat' => 'yyyy-MM-dd',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'с',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:left',
                    ]
                ]).yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_begin_to',
                    'value' => \Yii::$app->request->get('date_begin_to'),
                    'attribute'=>'date_begin',
                    'language' => 'ru-Ru',
                    // 'dateFormat' => 'yyyy-MM-dd',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'по',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:right',
                    ]
                ]),
                'contentOptions' => ['style'=>'text-align: center;'],
            ],
            // 'date_end',
            [
                'format' => ['date', 'php:d.m.Y H:i'], // здесь так зададим, чтоб убрать сек.
                'attribute' => 'date_end',
                'filter' => yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_end_from',
                    'value' => \Yii::$app->request->get('date_end_from'),
                    'attribute'=>'date_end',
                    'language' => 'ru-Ru',
                    // 'dateFormat' => 'yyyy-MM-dd',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'с',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:left',
                    ]
                ]).yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_end_to',
                    'value' => \Yii::$app->request->get('date_end_to'),
                    'attribute'=>'date_end',
                    'language' => 'ru-Ru',
                    // 'dateFormat' => 'yyyy-MM-dd',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'по',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:right',
                    ]
                ]),
                'contentOptions' => ['style'=>'text-align: center;'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                // 'template'=>'{update} &nbsp; {delete}',
                /*
                    строку выше для скрытия кнопки просмотр не будем использовать, поскольку
                    она удаляет сам код кнопки. Мы же из яваскрипта будем скрывать эту кнопку
                    и по клику на строке таблицы будем перенаправлять действие на эту кнопку
                    todo: при реализации появились трудности
                */
                'contentOptions' => ['style'=>'text-align: center;'],
            ],
        ],
    ]); ?>

</div>
<?php
// далее скрипты на JS
// $search = <<< JS
//     // скроем все ссылки-глаза. Но сами их оставим
//     $('span.glyphicon.glyphicon-eye-open').parent().hide();
//     // выбираем строку и переходим по скрытой ссылке-глазу
//     $('tbody>tr').click(function(event) {
//         // console.log($('td:last-child>a:first-child', this));
//         location.href =
//             $('td:last-child>a:first-child', this).attr('href');
//     });
// JS;
//     $this->registerJs($search, View::POS_READY);
// // далее CSS
// $this->registerCss("
//     tbody>tr:hover{
//         cursor:pointer;
//         background:#ECECEC !important; /* ато через одну выделяются. Движок Yii*/
//     }
// ");