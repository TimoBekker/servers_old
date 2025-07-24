<?php
/* партиал отображения index раздела справочники для модели SClaim */
use yii\grid\GridView;
use app\models\reference\SOrganization;
use app\models\registry\RContract;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        // 'id',
        'contract'=>[
            'attribute' => 'contract',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
                return RContract::findOne((int)$model->contract)->name;
            },
        ],
        'organization'=>[
            'attribute' => 'organization',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
                return SOrganization::findOne((int)$model->organization)->name;
            },
        ],
        'prime'=>[
        	'attribute' => 'prime',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
                if(!is_null($model->prime))
            	   return SOrganization::findOne((int)$model->prime)->name;
            },
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'header'=>'Действия',
            'template'=>'{update} &nbsp; {delete}',
            'urlCreator'=>function($action, $model, $key, $index) use ($refname) {
                return [$action, 'refid'=>$refname, 'id' => $key];
            },
            'headerOptions' => ['style'=>'text-align: center;'],
            'contentOptions' => ['style'=>'text-align: center;'],
        ],
    ],
]);