<?php
/* вывод списка справочников */
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */

$this->title = 'Редактирование справочников';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ctype-equipment-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
		<?php //= Html::a('Create Ctype Equipment', ['create'], ['class' => 'btn btn-success']) ?>
	</p>

<?php // var_dump($refsnames); ?>

	<?= GridView::widget([
		// данные
		'dataProvider' => $refsnames,
		// текст пагинатора (убираем он нам не нужен)
		'summary' => "",
		'columns' => [
			[
				'attribute' => 'name',
				'format' => 'HTML',
				'label' => 'СПРАВОЧНИКИ:',
			],
			// ['class' => 'yii\grid\ActionColumn']
		],
	]); ?>

</div>
<?php
// далее скрипты на JS
$search = <<< JS
	// вначале все клоузим
	$('.need-closed').parent().hide();
	// дадим загловкам один цвет фона
	jQuery('.separator.text-center').parent().css('background','#f5f5f5')
	// потом открываем те, которые есть в localStorage
	/*
	var arrStorage = {equip:'equip', soft:'soft'};
	for(var gaiver in arrStorage){
		// если свойство унаследовано - continue
		if (!arrStorage.hasOwnProperty(gaiver)) continue
		$('.need-closed.'+arrStorage[gaiver]).parent().show();
	}
	*/
	for (var i = 0; i < sessionStorage.length; i++) {
		var key = sessionStorage.key(i);
		if ( sessionStorage[key] == 'showedBlock') {
			$('.need-closed.'+key).parent().show();
		}
	}
	// потом селекторим, что надо и тоглим
    jQuery('.separator.text-center').parent().click(function(event) {
    	var clsname = $(this).children().attr("class");
    	var clsname = clsname.substring(0, clsname.length - 12);
    	var clsname = clsname.replace(' ', '.')
    	// alert($('.need-closed.'+clsname).parent().css('display'));
    	if ( $('.need-closed.'+clsname).parent().css('display') == 'none' ){
    		// alert('test');
    		sessionStorage[clsname] = "showedBlock";
    	} else {
    		sessionStorage[clsname] = "closedBlock";
    	}
        $('.need-closed.'+clsname).parent().toggle('fast');
    });
JS;
    $this->registerJs($search, View::POS_READY);
    // where $position can be View::POS_READY (the default),
    // or View::POS_HEAD, View::POS_BEGIN, View::POS_END

// далее CSS
$this->registerCss("
    .separator.text-center:hover{
        cursor: pointer;
        color:lightblue;
    }
");