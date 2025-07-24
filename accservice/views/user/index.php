<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\reference\SOrganization;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ответственные лица';
$this->params['breadcrumbs'][] = ['label' => 'Редактирование справочников', 'url' => ['refs/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (isset($notdeleted) && $notdeleted === true): ?>
    <?php // Модальное диалоговое окно ?>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <?php // <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ?>
                    <h4 class="modal-title" id="myModalLabel">
                        <?= $modalName = 'Данный элемент справочника невозможно удалить' ?>
                    </h4>
                </div>
                <div class="modal-body">
                    Удаление данного элемента справочника станет<br/>
                    возможным только после удаления всех записей<br/>
                    системы, использующих этот элемент !
                </div>
                <div class="modal-footer">
                    <?= Html::a('Закрыть', ['user/index'], ['class' => 'btn btn-primary'])?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
    ?>
<?php endif ?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сбросить весь поиск', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'username',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            // 'status',
            // 'created_at',
            // 'updated_at',
            'last_name' => [
                'attribute' => 'last_name',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->last_name, ['user/view', 'id' => $model->id]);
                },
            ],
            'first_name',
            'second_name' => [
                'attribute' => 'second_name',
                'value' => function($model){
                    return $model->second_name ?: null;
                }
            ],
            // 'organization',
            [
                'attribute' => 'organization',
                'value' => function($model){
                    return SOrganization::findOne((int)$model->organization)->name;
                },
                'filter' => ArrayHelper::map(SOrganization::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            'position' => [
                'attribute' => 'position',
                'value' => function($model){
                    return $model->position ?: null;
                }
            ],
            'email' => [
                'attribute' => 'email',
                'format' => 'email',
                'value' => function($model){
                    return $model->email ?: null;
                }
            ],
            // 'phone_work',
            // 'phone_cell',
            // 'skype',
            // 'additional',
            // 'leader',

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Действия',
                'contentOptions' => ['style'=>'text-align: center;'],
            ],
        ],
    ]); ?>

</div>
