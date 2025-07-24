<?php

use app\models\registry\RInformationSystem;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\reference\CVariantValidation;
use app\models\reference\CLevelProtection;
use app\models\reference\CLevelPrivacy;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RInformationSystemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Информационные системы';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rinformation-system-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить информационную систему', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сбросить весь поиск', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'name_short' => [
                'attribute' => 'name_short',
                'label' => 'Краткое наименование (неофициальное)',
                'format' => 'html',
                'value' => function($model){
                    return Html::a($model->name_short, ['infosys/view', 'id' => $model->id]);
                },
            ],
            'name_full' => [
                'attribute' => 'name_full',
                'label' => 'Полное наименование',
            ],
            // чтобы не делать подробный поиск отображаем описание, но вывод его сокращаем до 100 нач. символов
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
            [
                'label' => 'Оборудование',
                'format' => 'html',
                'value' => function($model) {
                    /* @var RInformationSystem $model */
                    if (empty($model->nnEquipmentInfosysContours)) return null;
                    $res = '';
                    foreach ($model->getNnEquipmentInfosysContours()->indexBy('equipment')->all() as $item) {
                        $ref = Html::a($item->equipmentModel->name, ['equip/view', 'id' => $item->equipment]);
                        $res .= <<< LABEL
<div><span style="white-space: nowrap">Имя: $ref</span>, <span style="white-space: nowrap">VMware-имя: {$item->equipmentModel->vmware_name}</span></div> <br>
LABEL;
                    }
                    return rtrim($res, "<br>");
                },
                'filter' => \app\models\registry\RInformationSystem::find()->select(['name_short','id'])->indexBy('id')->orderBy("name_short")->column(),
                'contentOptions' => ['data-index'=>24],
                'filterOptions' => ['data-index'=>24],
                'headerOptions' => ['data-index'=>24],
            ],
            /*
            'validation'=>[
                'attribute' => 'validation',
                // 'label'=>'Альтернативный загловок столбца',
                'value' => function($model){
                    if(!is_null($model->validation))
                        return CVariantValidation::findOne((int)$model->validation)->name;
                },
                'filter' => ArrayHelper::map(CVariantValidation::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            'protection'=>[
                'attribute' => 'protection',
                // 'label'=>'Альтернативный загловок столбца',
                'value' => function($model){
                    if(!is_null($model->protection))
                        return CLevelProtection::findOne((int)$model->protection)->name;
                },
                'filter' => ArrayHelper::map(CLevelProtection::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            'privacy'=>[
                'attribute' => 'privacy',
                'label'=>'Уровень конфиден-ти',
                'value' => function($model){
                    if(!is_null($model->privacy))
                        return CLevelPrivacy::findOne((int)$model->privacy)->name;
                },
                'filter' => ArrayHelper::map(CLevelPrivacy::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ],
            */

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
