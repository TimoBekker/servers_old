<?php
/* партиал отображения index раздела справочники для модели SClaim */
use yii\grid\GridView;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        // 'id',
        'dns_name',
        [
        	'attribute' => 'ip_address',
            'value' => function($model){
            	return long2ip($model->ip_address);
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