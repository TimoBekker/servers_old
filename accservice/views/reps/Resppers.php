<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RInformationSystem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ответственные за информационные системы';
$this->params['breadcrumbs'][] = 'Отчеты: Ответственные за информационные системы';
?>

<div class="reports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // var_dump($dataProvider);exit; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // 'information_system',
            [
                // 'header' => 'Наименование ИС',
                'label' => 'Наименование ИС',
                'attribute' => 'infosystemShortName',
                'format' => 'html',
                'value' => function($model){
                    // var_dump($model);exit;
                    return Html::a($model->infosystemShortName, ['infosys/view', 'id' => $model->information_system]);
                }
            ],
            [
                'label' => 'Вид ответственности',
                'attribute' => 'responsibilityName',
                /*'value' => function($model){
                    return $model->responsibility0->name;
                }*/
            ],
            [
                'label' => 'Ответственное лицо',
                'attribute' => 'responsePersonName',
                // 'attribute' => 'responsePerson.last_name',
                /*'value' => function($model){
                    return $model->responsePerson->last_name.' '.$model->responsePerson->first_name.' '.$model->responsePerson->second_name;
                },*/
            ],
            /*[
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'contentOptions' => ['style'=>'text-align: center;'],
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        return Html::a('', $url, [
                            'class' => 'glyphicon glyphicon-trash showdelpopup',
                            'title' => 'Удалить',
                            'data-method' => 'post'
                        ]);
                    },
                ],
            ],*/
        ],
    ]); ?>

    <?php // var_dump($dataProvider) ?>
</div>
