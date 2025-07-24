<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftwareInstalled */

$this->title = 'Учет установленной лицензии';
$this->params['breadcrumbs'][] = ['label' => 'Установленные лицензии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rsoftware-installed-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'showModalSwitcher' => $showModalSwitcher,
        'switcherId' => $switcherId,
    ]) ?>

</div>
