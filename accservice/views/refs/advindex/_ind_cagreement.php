<?php
/* партиал отображения index раздела справочники для модели CAgreement */
use yii\grid\GridView;
use app\models\reference\CAgreement;
use yii\helpers\Html;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        // 'id',
        'name' => [
            'attribute' => 'name',
            'format' => 'html',
            'value' => function($model){
                return Html::a($model->name, ['refs/view', 'refid' => 'CAgreement', 'id' => $model->id]);
            },
        ],
        'description',
        [
            'class' => 'yii\grid\ActionColumn',
            'header'=>'Действия',
            'template'=>'{view} {update} {delete}',
            'urlCreator'=>function($action, $model, $key, $index) use ($refname) {
                return [$action, 'refid'=>$refname, 'id' => $key];
            },
            'headerOptions' => ['style'=>'text-align: center;'],
            'contentOptions' => ['style'=>'text-align: center;'],
        ],
    ],
]);