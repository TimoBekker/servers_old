<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftwareInstalled */

// $this->title = $model->id;
$this->title = $model->software0->name.' '.$model->software0->version.' - '.$model->equipment0->name;
$this->params['breadcrumbs'][] = ['label' => 'Установленные лицензии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rsoftware-installed-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger showdelpopup',
            'data' => [
                // 'confirm' => 'Вы точно хотите удалить эту установленную лицензию?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h3><?= Html::encode("Основные данные:") ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            [
                'attribute' => '',
                'label' => 'Тип дистрибутива ПО',
                'value' => $model->software0->type0->name,
            ],
            'software' => [
                'attribute' => 'software',
                'label' => 'Наименование и версия дистрибутива ПО',
                'format' => 'html',
                'value' => Html::a(
                    $model->software0->name.' '.$model->software0->version,
                    ['soft/view', 'id' => $model->software0->id]
                ),

            ],
            'equipment' => [
                'attribute' => 'equipment',
                'label' => 'Место установки',
                'format' => 'html',
                'value' => Html::a($model->equipment0->name, ['equip/view', 'id' => $model->equipment0->id]),
            ],
            'description' => [
                'attribute' => 'description',
                'value' => $model->description ?: null,
            ],
            'date_commission:date',
            // 'bitrate',
            [
                'attribute' => 'bitrate',
                'value' => $model->bitrate ? $model->bitrate.' бит' : null,
            ]
        ],
    ]) ?>


    <h3><?= Html::encode("Дополнительные данные:") ?></h3>
    <?php

        // Связан с информационными системами
        $infosyss = '';
        foreach ($model->nnIsSoftinstalls as $value) {
            $infosyss .= Html::a($value->informationSystem->name_short, ['infosys/view', 'id' => $value->informationSystem->id]);
            $infosyss .= ', '.$value->informationSystem->name_full;
            $infosyss .= '<br/>';
        }

        // связь с установленным по
        $installsoft = '';
        foreach ($model->nnSoftinstallSoftinstalls as $value) {
            $href = $value->softwareInstalled2->software0->type0->name.' ';
            $href .= $value->softwareInstalled2->software0->name.' ';
            $href .= $value->softwareInstalled2->software0->version.' ';
            $equip = $value->softwareInstalled2->equipment0->name;
            $installsoft .= Html::a($href, ['softins/view', 'id' => $value->softwareInstalled2->id]);
            $installsoft .= '<ul>';
            $installsoft .= '<li>Описание: '.$value->softwareInstalled2->description.'</li>';
            $installsoft .= '<li> Установлено на: '.Html::a($equip, ['equip/view', 'id' => $value->softwareInstalled2->equipment0->id]).'</li>';
            $installsoft .= '</ul>';
            $installsoft .= '<br/>';
        }

    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Связь с информационными системами',
                'format' => 'html',
                'value'=> $infosyss ? $infosyss : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связь с другим установленным ПО',
                'format' => 'html',
                'value'=> $installsoft ? $installsoft : '<i style="color:#c55">(не задано)</i>' ,
            ],

        ],
    ]) ?>
</div>
