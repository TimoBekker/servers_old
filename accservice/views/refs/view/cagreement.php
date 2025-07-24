<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\reference\CAgreement;

/* @var $this yii\web\View */
/* @var $model app\models\reference\Название класса модели */

// Файл с соглашением
$documents = '';
foreach ($model->rDocumentations as $value) {
	// доку по заявке выводим без названия, просто скачать
    $documents .= 'Скачать: '.Html::a('', $value->url, ['class'=>'glyphicon glyphicon-download-alt']).'<br/>';
}

// Связанные заявки
$claims = '';
foreach ($model->sClaims as $value) {
	$claimDoc = $value->rDocumentations ? $value->rDocumentations[0]->url : null;
	$claims .= Html::a($value->name, ['refs/view', 'refid' => 'SClaim',  'id' => $value->id]);
	$claims .= $claimDoc ? ', Скачать: '.Html::a('', $claimDoc, ['class'=>'glyphicon glyphicon-download-alt']) : '';
	$claims .= '<br/>';
}


echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        // 'id',
        [
            'label' => 'Файл с соглашением',
            'format' => 'html',
            'value'=> $documents ? $documents : '<i style="color:#c55">(не задано)</i>' ,
        ],
        'name',
        'description' => [
        	'attribute' => 'description',
        	'value' => $model->description ?: null,
        ],
        [
            'label' => 'Связанные заявки',
            'format' => 'html',
            'value'=> $claims ? $claims : '<i style="color:#c55">(не задано)</i>' ,
        ],
    ],
]) ?>