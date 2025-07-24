<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\reference\CTypeEquipment */

$this->title = 'Редактирование элемента справочника: '.$model::$tableLabelName;
$this->params['breadcrumbs'][] = ['label' => 'Редактирование справочников', 'url' => ['refs/index']];
$this->params['breadcrumbs'][] = ['label' => $model::$tableLabelName, 'url' => ['refs/index', 'refid' => $refid]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ctype-equipment-update">

    <h1><?= Html::encode($this->title) ?></h1>

	<?php
		switch ($refid) {
			case 'SClaim':
			case 'CAgreement':
			case 'SOpenPort':
			case 'SPlacement':
			case 'SVariantAccessRemote':
			case 'SContractor':
			case 'SResponseWarrantySupport':
			case 'SResponseEquipment':
			case 'SResponseInformationSystem':
			case 'SIpdnsTable':
				// $partial = 'advforms/_form_sclaim';
				$partial = 'advforms/_form_'.mb_strtolower($refid);
				break;

			default:
				$partial = '_form'; // партиал по умолчанию для простых справочников
				break;
		}
	?>

    <?= $this->render($partial, [
        'model' => $model,
    ]) ?>

</div>
