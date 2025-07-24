<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\reference\CTypeSoftware;
use app\models\reference\CMethodLicense;
use app\models\reference\CTypeLicense;
use app\models\reference\SOrganization;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RSoftwareSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Дистрибутивы';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rsoftware-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить дистрибутив', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сбросить весь поиск', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'type'=>[
                'attribute' => 'typeName',
                'label'=>'Тип ПО',
                // 'value' => 'type0.name',
                'filter' => ArrayHelper::map(CTypeSoftware::find()->select('id, name')->asArray()->All(), 'name', 'name'),
            ],
            'type_license' => [
                'attribute' => 'type_license',
                'value' => 'typeLicense.name',
                'filter' => ArrayHelper::map(CTypeLicense::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            'name' => [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($model){
                    // var_dump($model);
                    return Html::a($model->name, ['soft/view', 'id' => $model->id]);
                },
            ],
            'version' => [
                'attribute' => 'version',
                'value' => function($model){
                    if(!$model->version)return null;
                    return $model->version;
                }
            ],
            'method_license' => [
                'attribute' => 'method_license',
                'value' => 'methodLicense.name',
                'filter' => ArrayHelper::map(CMethodLicense::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            // 'description',
            'count_license' => [
                'attribute' => 'count_license',
                'label' => 'Количество лицензий',
                'value' => function($model){
                    if($model->count_license === 0)return 'Бесконечное количество';
                    return $model->count_license;
                },
                'filterInputOptions' => ['placeholder' => '0 - Беск.кол-во', 'class' => 'form-control'],
            ],
            /*
            'date_end_license' => [
                'attribute' => 'date_end_license',
                'format' => 'date',
                'filter' => yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_end_license_from',
                    'value' => \Yii::$app->request->get('date_end_license_from'),
                    'attribute'=>'date_end_license',
                    'language' => 'ru-Ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'с',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:left',
                    ]
                ]).yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_end_license_to',
                    'value' => \Yii::$app->request->get('date_end_license_to'),
                    'attribute'=>'date_end_license',
                    'language' => 'ru-Ru',
                    'dateFormat' => 'dd.MM.yyyy',
                    'options' => [
                        'placeholder'=>'по',
                        'class' => 'form-control',
                        'style' => 'width: 50%; float:right',
                    ]
                ]),
                'contentOptions' => ['style'=>'text-align: center;'],
            ],
            'owner'=>[
                'attribute' => 'owner',
                // 'label'=>'Альтернативный загловок столбца',
                'value' => function($model){
                    if(!is_null($model->owner))
                        return SOrganization::findOne((int)$model->owner)->name;
                },
                'filter' => ArrayHelper::map(SOrganization::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            */
            // 'developer',
            // 'count_cores',
            // 'amount_ram',
            // 'volume_hdd',
            // 'inventory_number',
            [
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
            ],
        ],
    ]); ?>

</div>
