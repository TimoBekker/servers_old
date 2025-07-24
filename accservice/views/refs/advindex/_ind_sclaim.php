<?php
/* партиал отображения index раздела справочники для модели SClaim */
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
                return Html::a($model->name, ['refs/view', 'refid' => 'SClaim', 'id' => $model->id]);
            },
        ],
        // 'agreement',
        'agreement'=>[
        	'attribute' => 'agreement',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
            	return CAgreement::findOne((int)$model->agreement)->name;
            },
        ],
        // 'parent' => [
        //     'attribute' => 'parent',
        //     // 'label'=>'Альтернативный загловок столбца',
        //     // следующая вставка очень кривая, но как сделать по другому пока моего xp yii не хватило
        //     'value' => function ($model, $key, $index, $column) {
        //         if($model->findOne($key)->parent != null)
        //            return $model->findOne($model->findOne($key)->parent)->name;
        //     }
        // ],
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