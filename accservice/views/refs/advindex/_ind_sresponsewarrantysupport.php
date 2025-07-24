<?php
/* партиал отображения index раздела справочники для модели SClaim */
use yii\grid\GridView;
use app\models\User;
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