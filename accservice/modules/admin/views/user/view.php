<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->last_name.' '.$model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php

    // Ответственный за оборудование
    $equipments = '';
    foreach ($model->sResponseEquipments as $value) {
        $equipments .= $value->equipment0->type0->name;
        $equipments .= ' ';
        $equipments .= Html::a($value->equipment0->name, ['equip/view', 'id' => $value->equipment0->id]);
        $equipments .= ' ';
        $equipments .= $value->responsibility0->name;
        $equipments .= '<br/>';
    }
    // Ответственный за ИС
    $infosyss = '';
    foreach ($model->sResponseInformationSystems as $value) {
        $infosyss .= Html::a($value->informationSystem->name_short, ['infosys/view', 'id' => $value->informationSystem->id]);
        $infosyss .= ' ';
        $infosyss .= $value->responsibility0->name;
        $infosyss .= '<br/>';
    }

    // Ответственный за гарантийную поддержку по контракту
    $contracts = '';
    foreach ($model->sResponseWarrantySupports as $value) {
        $date_complete = $value->contract0->date_complete;
        $date_complete = $date_complete && $date_complete != '0000-00-00' ? (new \Datetime($value->contract0->date_complete))->format('d.m.Y') : '';
        $date_end_warranty = $value->contract0->date_end_warranty;
        $date_end_warranty = $date_end_warranty && $date_end_warranty != '0000-00-00' ? (new \Datetime($value->contract0->date_end_warranty))->format('d.m.Y') : '';
        $contracts .= Html::a($value->contract0->name, ['contr/view', 'id' => $value->contract0->id]).' ';
        $contracts .= '<ul>';
            $contracts .= $date_complete ? "<li>Окончание работ: {$date_complete}</li>" : '';
            $contracts .= $date_end_warranty ? "<li>Окончание гарантии: {$date_end_warranty}</li>" : '';
        $contracts .= '</ul>';
        $contracts .= '<br/>';
    }

    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            // 'username',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            // 'status',
            // 'created_at',
            // 'updated_at',
            'last_name',
            'first_name',
            'second_name' => [
                'attribute' => 'second_name',
                'value' => $model->second_name ?: null,
            ],
            [
                'attribute' => 'organization',
                'format' => 'html',
                'value' => Html::a($model->organization0->name, ['refs/view', 'refid' => 'SOrganization', 'id' => $model->organization0->id] ),
            ],
            'position' => [
                'attribute' => 'position',
                'value' => $model->position ?: null,
            ],
            'email' => [
                'attribute' => 'email',
                'format' => 'email',
                'value' => $model->email ?: null,
            ],
            'phone_work' => [
                'attribute' => 'phone_work',
                'value' => $model->phone_work ?: null,
            ],
            'phone_cell' => [
                'attribute' => 'phone_cell',
                'value' => $model->phone_cell ?: null,
            ],
            'skype' => [
                'attribute' => 'skype',
                'value' => $model->skype ?: null,
            ],
            'additional' => [
                'attribute' => 'additional',
                'value' => $model->additional ?: null,
            ],
            // 'leader',
            [
                'attribute' => 'leader',
                'format' => 'html',
                'value' => !is_null($model->leader0)
                    ?
                        Html::a(
                            $model->leader0->last_name.' '.$model->leader0->first_name.' '.$model->leader0->second_name,
                            ['user/view', 'id'=> $model->leader0->id]
                        )
                    :
                        null,

            ],
            [
                'label' => 'Ответственный за оборудование',
                'format' => 'html',
                'value'=> $equipments ? $equipments : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Ответственный за ИС',
                'format' => 'html',
                'value'=> $infosyss ? $infosyss : '<i style="color:#c55">(не задано)</i>' ,
            ],
            [
                'label' => 'Ответственный за гарантийную поддержку по контракту',
                'format' => 'html',
                'value' => $contracts ? $contracts : '<i style="color:#c55">(не задано)</i>',
            ],
        ],
    ]) ?>

</div>
