<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftware */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Дистрибутивы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rsoftware-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger showdelpopup',
            'data' => [
                // 'confirm' => 'Вы точно хотите удалить этот дитсрибутив?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h3><?= Html::encode("Основные данные:") ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'name' => [
                'attribute' => 'name',
                'label' => 'Наименование ПО'
            ],
            'type' => [
                // 'label'=>'альтернативный заголовок',
                'attribute' => 'type',
                'label' => 'Тип ПО',
                'value' => $model->type0->name // в этом виде мы можем так использовать Релейшн
            ],
            'version' => [
                'attribute' => 'version',
                'label' => 'Версия ПО',
                'value' => $model->version ?: null
            ],
            'description' => [
                'attribute' => 'description',
                'label' => 'Описание ПО',
                'value' => $model->description ?: null
            ],
            'type_license' => [
                'attribute' => 'type_license',
                'value' => !is_null($model->typeLicense) ?
                    $model->typeLicense->name
                    : null
            ],
            'count_license' => [
                'attribute' => 'count_license',
                'label' => 'Кол-во доступных лицензий',
                'value' => $model->count_license === 0 ? 'Бесконечное количество' : $model->count_license
            ],
            'date_end_license' => [
                'attribute' => 'date_end_license',
                'format' => 'date',
                'label' => 'Дата окончания действия лицензии',
            ],
            'owner' => [
                'attribute' => 'owner',
                'label' => 'Организация-владелец лицензии',
                'value' => !is_null($model->owner0) ?
                    $model->owner0->name
                    : null
            ],
            'method_license' => [
                'attribute' => 'method_license',
                'value' => !is_null($model->methodLicense) ?
                    $model->methodLicense->name
                    : null
            ],
            'developer' => [
                'attribute' => 'developer',
                'value' => !is_null($model->developer0) ?
                    $model->developer0->name
                    : null
            ],
            'count_cores' => [
                'attribute' => 'count_cores',
                'label' => 'Требуемое кол-во ядер CPU',
            ],
            'amount_ram' => [
                'attribute' => 'amount_ram',
                'label' => 'Требуемый объем ОЗУ',
                'value' => $model->amount_ram ? (string)($model->amount_ram * 10).' Мб' : null
            ],
            'volume_hdd' => [
                'attribute' => 'volume_hdd',
                'label' => 'Требуемый объем HDD',
                'value' => $model->volume_hdd ? (string)($model->volume_hdd / 10).' Гб' : null
            ],
            'inventory_number' => [
                'attribute' => 'inventory_number',
                'value' => $model->inventory_number ?: null
            ],
        ],
    ]) ?>

    <h3><?= Html::encode("Дополнительные данные:") ?></h3>
    <?php
        // Связанные контракты
        $contracts = '';
        foreach ($model->nnSoftwareContracts as $value) {
            $date_complete = $value->contract0->date_complete;
            $date_complete = $date_complete && $date_complete != '0000-00-00' ? (new \Datetime($value->contract0->date_complete))->format('d.m.Y') : '';
            $date_end_warranty = $value->contract0->date_end_warranty;
            $date_end_warranty = $date_end_warranty && $date_end_warranty != '0000-00-00' ? (new \Datetime($value->contract0->date_end_warranty))->format('d.m.Y') : '';
            // $cost = money_format('%i', $value->contract0->cost); // функция не определена в Windows
            $cost = $value->contract0->cost ? number_format($value->contract0->cost, 2, ',', ' ').' руб.' : '';
            $docs = $value->contract0->url ?: '';
            $contracts .= Html::a($value->contract0->name, ['contr/view', 'id' => $value->contract0->id]).' ';
            $contracts .= '<ul>';
                $contracts .= $date_complete ? "<li>Окончание работ: {$date_complete}</li>" : '';
                $contracts .= $date_end_warranty ? "<li>Окончание гарантии: {$date_end_warranty}</li>" : '';
                $contracts .= $cost ? "<li>Стоимость: {$cost}</li>" : '';
                $contracts .= $docs ? "<li>Скачать: ".Html::a('', $docs, ['class'=>'glyphicon glyphicon-download-alt'])."</li>" : '';
            $contracts .= '</ul>';
            $contracts .= '<br/>';
        }

        // Связан с информационными системами
        $infosyss = '';
        foreach ($model->nnIsSoftwares as $value) {
            $infosyss .= Html::a($value->informationSystem->name_short, ['infosys/view', 'id' => $value->informationSystem->id]);
            $infosyss .= ', '.$value->informationSystem->name_full;
            $infosyss .= '<br/>';
        }

        // Связан с другими дистрибутивами. Вытаскиваем запросом, т.к. нам надо упорядочить по require_number для вывода
        $softs = '';
        $prev_rn = null;
        $first = true;
        foreach (app\models\relation\NnSoftwareSoftware::find()->where(['software1' => $model->id])->orderBy('require_number')->All() as $value) {

            if(!is_null($value->require_number) && $value->require_number == $prev_rn)
                $delimiter = ' ИЛИ ';
            else
                $delimiter = '<br/>';

            if($first){ $delimiter = ''; $first = !$first; }

            $softs .= $delimiter;
            $href = $value->software20->type0->name.' ';
            $href .= $value->software20->name.' ';
            $href .= $value->software20->version.' ';
            $softs .= Html::a($href, ['soft/view', 'id' => $value->software20->id]);

            $prev_rn = $value->require_number;

        }

        // Связь с установленным по
        $installsoft = '';
        foreach ($model->rSoftwareInstalleds as $value) {
            $href = $value->software0->type0->name.' ';
            $href .= $value->software0->name.' ';
            $href .= $value->software0->version.' ';
            $equip = $value->equipment0->name;
            $installsoft .= Html::a($href, ['softins/view', 'id' => $value->id]);
            $installsoft .= '<ul>';
            $installsoft .= '<li>Описание: '.$value->description.'</li>';
            $installsoft .= '<li> Установлено на: '.Html::a($equip, ['equip/view', 'id' => $value->equipment0->id]).'</li>';
            $installsoft .= '</ul>';
            $installsoft .= '<br/>';
        }

        // Документация
        $documents = '';
        foreach ($model->rDocumentations as $value) {
            $documents .= $value->name.' '.$value->description.' ';
            $documents .= Html::a('', $value->url, ['class'=>'glyphicon glyphicon-download-alt']).'<br/><br/>';
        }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Связанные контракты',
                'format' => 'html',
                'value'=> $contracts ? $contracts : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связан с информационными системами',
                'format' => 'html',
                'value'=> $infosyss ? $infosyss : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связан с другими дистрибутивами',
                'format' => 'html',
                'value'=> $softs ? $softs : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связь с установленным ПО',
                'format' => 'html',
                'value'=> $installsoft ? $installsoft : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Документация',
                'format' => 'html',
                'value'=> $documents ? $documents : '<i style="color:#c55">(не задано)</i>' ,
            ],
        ],
    ]) ?>
</div>
