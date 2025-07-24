<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\reference\Название класса модели */

// Сотрудники организации
$users = '';
foreach ($model->users as $value) {
    $fio = $value->last_name.' '.$value->first_name.' '.$value->second_name;
    $users .= Html::a($fio, ['user/view', 'id' => $value->id]);
    $users .= ', '.$value->position;
    $users .= '<br/>';
}

// Связанные контракты
$contracts = '';
foreach ($model->sContractors as $value) {
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

// Разработчик дистрибутивов ПО
$softs = '';
foreach ($model->rSoftwares as $value) {
    $href = $value->name.' '.$value->version;
    $softs .= $value->type0->name;
	$softs .= ' '.Html::a($href, ['soft/view', 'id' => $value->id]);
	$softs .= '<br/>';
}


echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        // 'id',
        'name',
        'address' => [
            'attribute' => 'address',
            'value' => $model->address ?: null,
        ],
        'phone' => [
            'attribute' => 'phone',
            'value' => $model->phone ?: null,
        ],
        'site' => [
            'attribute' => 'site',
            'value' => $model->site ?: null,
        ],
        'parent' => [
            'attribute' => 'parent',
            'format' => 'html',
            'value' => $model->parent ? Html::a($model->parent0->name, ['refs/view', 'refid' => 'SOrganization', 'id' => $model->parent0->id] ) : null,
        ],
        [
            'label' => 'Сотрудники организации',
            'format' => 'html',
            'value'=> $users ? $users : '<i style="color:#c55">(не задано)</i>' ,
        ],
        [
            'label' => 'Связанные контракты',
            'format' => 'html',
            'value'=> $contracts ? $contracts : '<i style="color:#c55">(не задано)</i>' ,
        ],
        [
            'label' => 'Разработчик дистрибутивов ПО', // т.е. является разработчиком дистрибутивов
            'format' => 'html',
            'value' => $softs ? $softs : '<i style="color:#c55">(не задано)</i>',
        ],
    ],
]) ?>