<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управление группами и правами';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать группу', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сбросить весь поиск', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'type',
            // 'name',
            'alias' => [
                'attribute' => 'alias',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->alias, ['authitem/view', 'id' => $model->name]);
                },
            ],
            'description:ntext' => [
                'attribute' => 'description',
                'value' => function($model){
                    if(!$model->description)return null;
                    if(mb_strlen($model->description) > 100)
                        return mb_substr($model->description, 0, 100).'...';
                    else
                        return $model->description;
                },
            ],
            // 'rule_name',
            // 'data:ntext',
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'contentOptions' => ['style'=>'text-align: center; width: 110px'],
                'headerOptions' => ['style'=>'text-align: center;'],
                'template' => '{view} {update} {rules} {relay} {delete}',
                'buttons' => [
                    /*'view' => function ($url, $model, $key) {
                        return $model->type === 2 ? Html::a('', $url, ['class' => 'glyphicon glyphicon-eye-open', 'title' => 'Просмотр']) : '';
                    },*/
                    'update' => function ($url, $model, $key) {
                        return $model->type === 2 ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => 'Редактировать']) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        return $model->type === 2 ? Html::a('', $url, ['data-method' => 'post', 'class' => 'glyphicon glyphicon-trash', 'title' => 'Удалить']) : '';
                    },
                    'rules' => function ($url, $model, $key) {
                        // return $searchModel->status === 'editable' ? Html::a('Update', $url) : '';
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-wrench', 'title' => 'Задать права группы']);
                    },
                    'relay' => function ($url, $model, $key) {
                        // return $searchModel->status === 'editable' ? Html::a('Update', $url) : '';
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-link', 'title' => 'Задать связи с данными']);
                    }
                ],
            ],
        ],
    ]); ?>

</div>
