<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\registry\RLegalDoc */

$this->title = 'Добавление НПА';
$this->params['breadcrumbs'][] = ['label' => 'НПА', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rlegal-doc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'showModalSwitcher' => $showModalSwitcher,
        'switcherId' => $switcherId,
    ]) ?>

</div>
