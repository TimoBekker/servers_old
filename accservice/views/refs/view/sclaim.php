<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\reference\CAgreement;

/* @var $this yii\web\View */
/* @var $model app\models\reference\Название класса модели */

// Файл с заявкой
$documents = '';
foreach ($model->rDocumentations as $value) {
	// доку по заявке выводим без названия, просто скачать
    $documents .= 'Скачать: '.Html::a('', $value->url, ['class'=>'glyphicon glyphicon-download-alt']).'<br/>';
}

// Соглашение
$agreement = '';
if($model->agreement0){
	$agreeDoc = $model->agreement0->rDocumentations ? $model->agreement0->rDocumentations[0]->url : null;
	$agreement = Html::a($model->agreement0->name, ['refs/view', 'refid' => 'CAgreement', 'id' => $model->agreement0->id]);
	$agreement .= ', '.$model->agreement0->description;
	$agreement .= $agreeDoc ? ', Скачать: '.Html::a('', $agreeDoc, ['class'=>'glyphicon glyphicon-download-alt']) : '';
}

// Связанное оборудование
$equipments = '';
foreach ($model->nnEquipmentClaims as $value) {
	$equipments .= $value->equipment0->type0->name.' ';
	$equipments .= Html::a($value->equipment0->name, ['equip/view', 'id' => $value->equipment0->id]);
	$equipments .= '<br/>';
}


echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        // 'id',
        [
            'label' => 'Файл с заявкой',
            'format' => 'html',
            'value'=> $documents ? $documents : '<i style="color:#c55">(не задано)</i>' ,
        ],
        'name',
        [
            'label' => 'Соглашение',
            'format' => 'html',
            'value'=> $agreement ? $agreement : '<i style="color:#c55">(не задано)</i>' ,
        ],
        [
            'label' => 'Связанное оборудование',
            'format' => 'html',
            'value' => $equipments ? $equipments : '<i style="color:#c55">(не задано)</i>',
        ],
    ],
]) ?>