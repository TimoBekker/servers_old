<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RContract */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Контракты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('_common/_deletemodal'); ?>

<div class="rcontract-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактирование', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удаление', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger showdelpopup',
            'data' => [
                // 'confirm' => 'Вы точно хотите удалить данный контракт?',
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
            'name',
            'contract_subject' => [
                'attribute'=>'contract_subject',
                'label'=>'Предмет',
                'value' => $model->contract_subject ?: null
            ],
            'requisite' => [
                'attribute'=>'requisite',
                'value' => $model->requisite ?: null
            ],
            // 'cost',
            [
                'attribute' => 'cost',
                'value' => $model->cost ? number_format($model->cost, 2, ',', ' ').' руб.' : null
            ],
            'date_complete:date',
            'date_begin_warranty:date',
            'date_end_warranty:date',
        ],
    ]) ?>

    <h3><?= Html::encode("Дополнительные данные:") ?></h3>

    <?php
        // Ответственные за гарантийную поддержку
        $responsePersons ='';
        foreach ($model->sResponseWarrantySupports as $value) {
            $fio = $value->responsePerson->last_name.' '.$value->responsePerson->first_name.' '
                                                                                .$value->responsePerson->second_name;
            $responsePersons .= Html::a($fio, ['user/view', 'id' => $value->responsePerson->id]).', ';
        }
        $responsePersons = mb_substr($responsePersons, 0, -2); // убираем последнюю запятую

        // Исполнители по контракту
        $contractors = '';
        foreach ($model->sContractors as $value) {
            $contractors .= $value->prime ? 'Субподрядчик' : 'Генподрядчик';
            $contractors .= ' '.Html::a($value->organization0->name, '#');
            $contractors .= '<br/>';
        }

        // Связь с информационными системами
        $infosyss = '';
        foreach ($model->nnIsContracts as $value) {
            $infosyss .= Html::a($value->informationSystem->name_short, ['infosys/view', 'id' => $value->informationSystem->id]);
            $infosyss .= ', '.$value->informationSystem->name_full;
            $infosyss .= '<br/>';
        }

        // Связь с дистрибутивами ПО
        $soft = '';
        foreach ($model->nnSoftwareContracts as $value) {
            $href = $value->software0->type0->name.' ';
            $href .= $value->software0->name.' ';
            $href .= $value->software0->version.' ';
            $soft .= Html::a($href, ['soft/view', 'id' => $value->software0->id]);
            $soft .= '<br/>';
        }

        // var_dump($model->nnLdocContracts);
        $ldocs = '';
        foreach ($model->nnLdocContracts as $value) {
            $date = $value->legalDoc->date;
            $date = $date && $date != '0000-00-00' ? (new \Datetime($value->legalDoc->date))->format('d.m.Y') : '';
            $docs = $value->legalDoc->url ?: '';
            $ldocs .= Html::a($value->legalDoc->name, ['ldoc/view', 'id' => $value->legalDoc->id]).' ';
            $ldocs .= $date ?: '';
            $ldocs .= $value->legalDoc->status ? ' Отменен' : ' Действующий';
            $ldocs .= $docs ? ' Скачать: '.Html::a('', $docs, ['class'=>'glyphicon glyphicon-download-alt']) : '';
            $ldocs .= '<br/>';
        }

    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Ответственные за гарантийную поддержку',
                'format' => 'html',
                'value'=> $responsePersons ? $responsePersons : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Исполнители по контракту',
                'format' => 'html',
                'value'=> $contractors ? $contractors : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связь с информационными системами',
                'format' => 'html',
                'value'=> $infosyss ? $infosyss : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Связь с дистрибутивами ПО',
                'format' => 'html',
                'value'=> $soft ? $soft : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Cвязь с нормативно-правовыми актами',
                'format' => 'html',
                'value'=> $ldocs ? $ldocs : '<i style="color:#c55">(не задано)</i>' ,
            ],
        ],
    ]) ?>

</div>
