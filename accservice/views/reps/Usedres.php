<?php

use yii\bootstrap\Collapse;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\registry\RInformationSystem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Используемые ресурсы';
$this->params['breadcrumbs'][] = 'Отчеты: Используемые ресурсы';
?>

<div class="reports-index">

    <h1><?= Html::encode($this->title); ?></h1><?php

	function formatSize($size, $inMiB = true) {
		if ($size == null) {
			return $size;
		}
		$units = [['МБ', 'ГБ', 'ТБ'], ['МиБ', 'ГиБ', 'ТиБ']];
		$currentUnit = 0;
		while ($size >= ($inMiB ? 1024 : 1000)) {
			$size /= ($inMiB ? 1024 : 1000);
			++$currentUnit;
		}
		return round($size, 2).' '.$units[$inMiB ? 1 : 0][$currentUnit];
	}

	$items = [];
	foreach ($arrDataProvider as $dataProvider) {
		$items[] = [
			'label' => $dataProvider['typename'],
			'content' => GridView::widget([
				'dataProvider' => $dataProvider['provider'],
				'showFooter' => true,
				'summary' => '',
				'caption' => '',
				'columns' => [
					['class' => 'yii\grid\SerialColumn', 'footer' => 'Суммарно:'],
					[
						'attribute' => 'name',
						'contentOptions' => ['style' => 'width: 40%'],
						'format' => 'html',
						'value' => function ($model) {
							return Html::a($model->name, ['equip/view', 'id' => $model->id]);
						},
					],
					[
						'attribute' => 'count_cores',
						'value' => function ($model) {
							return $model->count_processors.'x'.$model->count_cores;
						//return $model->count_cores == '' ?: $model->count_cores * $model->count_processors;
						},
						'footer' => intval($dataProvider['sum_params']['sum_cc']),
					],
					[
						'attribute' => 'amount_ram',
						'footer' => formatSize($dataProvider['sum_params']['sum_ar']),
						'value' => function ($model) {
							return formatSize($model->amount_ram);
						},
					],
					[
						'attribute' => 'volume_hdd',
						'footer' => formatSize($dataProvider['sum_params']['sum_vh']),
						'value' => function ($model) {
							return formatSize($model->volume_hdd);
						},
					],
				],
			]),
		];
	}
	?>
    <?= Collapse::widget([
		'items' => $items,
	]); ?>

    <?php // var_dump($dataProvider)?>
</div>
