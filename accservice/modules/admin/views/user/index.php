<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\reference\SOrganization;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управление пользователями';
// $this->params['breadcrumbs'][] = $this->title;
?>

<?php // модальное окно ?>
<?php if ($confirmdelete && $id): ?>
    <?php echo $this->render('_common/_deletemodal', ['id' => $id]); ?>
    <?php $this->registerJs("$('#myModal').modal({'backdrop':'static'});", \yii\web\View::POS_READY); ?>
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
                'headerOptions' => ['style'=>'text-align: center;'],
                'template' => '{view} {update} {signup} {delete}',
                'buttons' => [
                    'signup' => function ($url, $model, $key) {
                        return Html::a('', $url, ['class' => 'glyphicon glyphicon-wrench', 'title' => 'Задать Логин и Пароль']);
                    }
                ],
            ],
        ],
    ]); ?>

</div>
