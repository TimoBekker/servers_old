<?php
/* партиал отображения index раздела справочники для модели CAgreement */
use yii\grid\GridView;
use yii\helpers\Html;
// use app\models\reference\CAgreement;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        // 'id',
        'name' => [
            'attribute' => 'name',
            'format' => 'html',
            'value' => function($model){
                return Html::a($model->name, ['refs/view', 'refid' => 'SOrganization', 'id' => $model->id]);
            },
        ],
        'address' => [
            'attribute' => 'address',
            'value' => function ($model) {
                return $model->address ? : null;
            }
        ],
        'phone' => [
            'attribute' => 'phone',
            'value' => function ($model) {
                return $model->phone ? : null;
            }
        ],
        'site' => [
            'attribute' => 'site',
            'format' => 'raw', // надо запомнить, в отличие от html не вырезает дополнительно передаваемые теги
            'value' => function ($model) {
                return $model->site ? Html::a($model->site, $model->site, ['target'=>'_blank']) : null;
                // return $model->site ? "<a href='$model->site' 'target = '_blank'>$model->site</a>" : null;
            }
        ],
        'parent' => [
            'attribute' => 'parent',
            // 'label'=>'Альтернативный загловок столбца',
            // следующая вставка очень кривая, но как сделать по другому пока моего xp yii не хватило
            'value' => function ($model, $key, $index, $column) {
                if($model->findOne($key)->parent != null)
                    return $model->findOne($model->findOne($key)->parent)->name;
            }
        ],
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