<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RLegalDoc */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'НПА', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rlegal-doc-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger showdelpopup',
            'data' => [
                // 'confirm' => 'Вы точно хотите удалить этот НПА?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h3><?= Html::encode("Основные данные:") ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            // 'url:url',
            [
                'attribute' => 'url',
                'label' => 'Файл с контрактом',
                'format' => "html",
                'value' => $model->url ? "<a href='{$model->url}'>".basename($model->url)."</a>" : null
            ],
            // 'type',
            'type' => [
                'attribute' => 'type',
                // 'label'=>'альтернативный заголовок',
                'value' => $model->type0->name // в этом виде мы можем так использовать Релейшн
            ],
            'name',
            'date:date',
            'number' => [
                'attribute' => 'number',
                'value' => $model->number ?: null
            ],
            'regulation_subject' => [
                'attribute' => 'regulation_subject',
                'value' => $model->regulation_subject ?: null
            ],
            // 'status',
            [
                'attribute' => 'status',
                'label'=>'Статус',
                'value' => $model->status == 0 ? 'Действующий' : 'Отменен'
            ],
        ],
    ]) ?>

    <h3><?= Html::encode("Дополнительные данные:") ?></h3>

    <?php

        // Связь с контрактами
        $contracts = '';
        foreach ($model->nnLdocContracts as $value) {
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

        // Связь с информационными системами
        $infosyss = '';
        foreach ($model->nnLdocIs as $value) {
            $infosyss .= Html::a($value->informationSystem->name_short, ['infosys/view', 'id' => $value->informationSystem->id]);
            $infosyss .= ', '.$value->informationSystem->name_full;
            $infosyss .= '<br/>';
        }

        // Ответственные за оборудование в рамках данного НПА
        $responseEquipWithThisNPA = ''; $rewtn = &$responseEquipWithThisNPA;
        foreach ($model->sResponseEquipments as $value) {
            $fio = $value->responsePerson->last_name.' '.$value->responsePerson->first_name.' '
                                                                                .$value->responsePerson->second_name;
            $rewtn .= Html::a($fio, ['user/view', 'id' => $value->responsePerson->id]).', ';
        }
        $rewtn = mb_substr($rewtn, 0, -2); // убираем последнюю запятую

        // Ответственные за ИС в рамках данного НПА
        $responseInfosysWithThisNPA = ''; $riwtn = &$responseInfosysWithThisNPA;
        foreach ($model->sResponseInformationSystems as $value) {
            $fio = $value->responsePerson->last_name.' '.$value->responsePerson->first_name.' '
                                                                                .$value->responsePerson->second_name;
            $riwtn .= Html::a($fio, ['user/view', 'id' => $value->responsePerson->id]).', ';
        }
        $riwtn = mb_substr($riwtn, 0, -2); // убираем последнюю запятую
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Связь с контрактами',
                'format' => 'html',
                'value'=> $contracts ? $contracts : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связь с информационными системами',
                'format' => 'html',
                'value'=> $infosyss ? $infosyss : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Ответственные за оборудование в рамках данного НПА',
                'format' => 'html',
                'value'=> $responseEquipWithThisNPA ? $responseEquipWithThisNPA : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Ответственные за ИС в рамках данного НПА',
                'format' => 'html',
                'value'=> $responseInfosysWithThisNPA ? $responseInfosysWithThisNPA : '<i style="color:#c55">(не задано)</i>' ,
            ],
        ],
    ]) ?>
</div>
