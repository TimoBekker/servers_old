<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\User;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Просмотр логов системы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rlog-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p> -->
        <?php // = Html::a('Create Rlog', ['create'], ['class' => 'btn btn-success']) ?>
    <!-- </p> -->

    <?php if ( $showdeleteform ): ?>
        <?= $this->render('_dellogs.php', []) ?>
    <?php endif ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'code',
            'grade',
            //'date_emergence',
            [
                'attribute' => 'date_emergence',
                'format'=> 'datetime',
                'filter' => yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_emergence_from',
                    'value' => \Yii::$app->request->get('date_emergence_from'),
                    'attribute'=>'date_emergence',
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
                    'name' => 'date_emergence_to',
                    'value' => \Yii::$app->request->get('date_emergence_to'),
                    'attribute'=>'date_emergence',
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
            // 'user',
            [
                'attribute' => 'user',
                /*'value' => function($searchModel){
                    return $searchModel->user0->last_name.' '.
                            mb_substr($searchModel->user0->first_name, 0, 1).'.'.
                            mb_substr($searchModel->user0->second_name, 0, 1).'. '.
                            '('.$searchModel->user0->organization0->name.')';
                },*/
/*                'filter' => ArrayHelper::map(User::find()->
                    select('`id`, concat(`last_name`, `second_name`) as `last_name`')->
                    asArray()->
                    All(),
                    'id', 'last_name'),*/

                /*'filter' => ArrayHelper::map(Yii::$app->db->createCommand(
                    'select `us`.`id`,
                            concat(`us`.`last_name`, " ", `us`.`first_name`, " ( ", `sor`.`name`, " )" ) as `usname`
                       from user us, s_organization sor
                      where us.organization = sor.id'
                    )
                    ->queryAll(), 'id', 'usname'),*/

            ],
            'content',
            /*[
                'attribute' => 'object_before',
                'header' => 'Объект до',
                'contentOptions' => ['style'=>'min-width:200px'],
            ],
            [
                'attribute' => 'object_after',
                'header' => 'Объект после',
                'contentOptions' => ['style'=>'min-width:200px'],
            ]*/
            [
                'header' => 'Действия',
                'format' => "raw",
                'value' => function ($model, $key) {
                    if ($model->code == 101) {
                        return Html::a('Сравнить', Url::to(["/admin/log/compare"]), [
                            // 'class' => 'glyphicon glyphicon-trash showdelpopup',
                            'title' => 'Сравнить',
                            'data-id' => $key,
                            'onclick' => '
                                let url = $(this).attr("href");
                                let datid = $(this).attr("data-id");
                                $.post(url, {id:datid}, function(data){
                                    $(".custom-modal-body-before").html("До модификации<pre>"+data.object_before+"</pre>");
                                    $(".custom-modal-body-after").html("После модификации<pre>"+data.object_after+"</pre>");
                                    $(".custom-modal").show();
                                });
                                return false;
                            '
                        ]);
                    }
                    return false;
                },
            ],
            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

<?php
    $this->registerCss(<<<CSS
        .custom-modal{
            position: fixed;
            top:50px;
            bottom: 0px;
            left: 0px;
            right: 0px;
            background: white;
            overflow-y: auto;
            display: none;
            z-index: 200;
        }
        .custom-modal-body pre{
            line-height: 0.9;
            font-family: sans-serif;
        }
CSS
);
?>

<div class="custom-modal">
    <div class="custom-modal-header  container-fluid"><h2>Сравнение данных объекта</h2><button type="button" class="btn btn-default pull-right" onclick="$('.custom-modal').hide()">Закрыть</button></div>
    <div class="custom-modal-body container-fluid">
        <div class="row">
            <div class="col-md-6 custom-modal-body-before"></div>
            <div class="col-md-6 custom-modal-body-after"></div>
        </div>
    </div>
</div>