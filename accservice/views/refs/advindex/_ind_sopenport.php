<?php
/* партиал отображения index раздела справочники для модели SClaim */
use yii\grid\GridView;
use app\models\reference\CProtocol;
use app\models\registry\REquipment;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        // 'id',
        'equipment' => [
        	'attribute' => 'equipment',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
            	return REquipment::findOne((int)$model->equipment)->name;
            },
        ],
        'port_number',
        'protocol'=>[
        	'attribute' => 'protocol',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
            	return CProtocol::findOne((int)$model->protocol)->name;
            },
        ],
        'description',

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