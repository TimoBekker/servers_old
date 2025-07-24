<?php

use yii\helpers\Html;
use yii\grid\GridView;

use app\models\registry\RSoftware;
use app\models\registry\REquipment;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RSoftwareInstalledSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Установленные лицензии';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rsoftware-installed-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить установленную лицензию', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сбросить весь поиск', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'software'=>[
                'attribute' => 'software',
                'format' => 'html',
                'value' => function($model){
                    return Html::a(
                        $model->software0->type0->name
                        .', '
                        .$model->software0->name
                        .' '
                        .$model->software0->version, ['softins/view', 'id' => $model->id]);
                    // return RSoftware::findOne((int)$model->software)->name;
                },
                'filter' => ArrayHelper::map(
                    RSoftware::find()
                    ->select(['r_software.id', 'concat(
                                                    cts.name, ", ",
                                                    r_software.name, " ",
                                                    r_software.version
                                                ) as name'])
                    ->join('INNER JOIN', 'c_type_software cts', 'cts.id = r_software.type')
                    ->orderBy('cts.name, r_software.name, r_software.version')
                    ->asArray()
                    ->All(), 'id', 'name'
                ),
            ],
            'equipment'=>[
                'attribute' => 'equipment',
                'value' => function($model){
                    return REquipment::findOne((int)$model->equipment)->name;
                },
                'filter' => ArrayHelper::map(REquipment::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name'),
            ],
            'description' => [
                'attribute' => 'description',
                'value' => function($model){
                    if(!$model->description)return null;
                    if(mb_strlen($model->description) > 100)
                        return mb_substr($model->description, 0, 100).'...';
                    else
                        return $model->description;
                },
            ],
            'date_commission' => [
                'attribute' => 'date_commission',
                'format' => 'date',
                'filter' => yii\jui\DatePicker::widget([
                    // 'model'=> $searchModel,
                    'name' => 'date_commission_from',
                    'value' => \Yii::$app->request->get('date_commission_from'),
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
                    'name' => 'date_commission_to',
                    'value' => \Yii::$app->request->get('date_commission_to'),
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
            'bitrate'=>[
                'attribute' => 'bitrate',
                'value' => function($model){
                    if(!$model->bitrate)return null;
                    return $model->bitrate.' бит';
                },
                'filter' => [ 32 => '32 бит', 64 => '64 бит', ]
            ],

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
