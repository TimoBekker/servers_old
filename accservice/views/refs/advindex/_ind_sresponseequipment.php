<?php
/* партиал отображения index раздела справочники для модели SClaim */
use yii\grid\GridView;
use app\models\User;
use app\models\registry\REquipment;
use app\models\registry\RLegalDoc;
use app\models\reference\CVariantResponsibility;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        // 'id',
        'equipment'=>[
            'attribute' => 'equipment',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
                return REquipment::findOne((int)$model->equipment)->name;
            },
        ],
        'responsibility'=>[
            'attribute' => 'responsibility',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
                return CVariantResponsibility::findOne((int)$model->responsibility)->name;
            },
        ],
        'response_person'=>[
            'attribute' => 'response_person',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
                $res = Yii::$app->db->createCommand(
                    'select `us`.`id`,
                            concat(`us`.`first_name`, " ", `us`.`last_name`, " ( ", `sor`.`name`, " )" ) as `usname`
                      from user us, s_organization sor
                     where us.organization = sor.id and `us`.`id` = :id'
                )->bindValue(':id', (int)$model->response_person)->queryOne();
                // var_dump($res);
                return $res['usname'];
            },
        ],
        'legal_doc'=>[
            'attribute' => 'legal_doc',
            // 'label'=>'Альтернативный загловок столбца',
            'value' => function($model){
                if(!is_null($model->legal_doc))
                    return RLegalDoc::findOne((int)$model->legal_doc)->name;
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