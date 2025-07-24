<?php
use yii\web\View;
use yii\helpers\Html;
use app\models\reference\SOpenPort;
?>

<?php if ($arrOpenPorts = $model->sOpenPorts): ?>
    <?= Html::script('var sopenPortMax = '.(count($arrOpenPorts)-1), []); ?>
    <?php foreach ($arrOpenPorts as $keyv => $v): ?>

        <?php // var_dump($v->); ?>
        <?= $form->beginField(new SOpenPort, "[{$keyv}]sopenports"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="sopenport-sopenports">Порты, прослушиваемые на оборудовании</label>
            <?php endif ?>
	        <div class="input-group">
	            <?= Html::input(
	                'text',
	                "SOpenPort[{$keyv}][port_number]",
	                $v->port_number,
	                [
	                	'class' => 'form-control',
	                	'placeholder'=>'Номер порта',
	                	// 'required'=>true
	                ]
	            ) ?>
	            <span class="input-group-btn" style="width:0px"></span>
	            <?= Html::DropDownList(
	                "SOpenPort[{$keyv}][protocol]",
	                !is_null($v->protocol) ? $v->protocol0->id : false,
	                $model->indexedProtocols,
	                [
	                    'prompt'=>'Протокол: не выбран',
	                    'class'=>'form-control',
	                    'style'=>'z-index:0'
	                ]
	            )?>
	            <span class="input-group-btn" style="width:0px"></span>
	            <?= Html::input(
	                'text',
	                "SOpenPort[{$keyv}][description]",
	                $v->description,
	                [
	                	'class' => 'form-control',
	                	'placeholder'=>'Описание',
	                	'maxlength'=>'255',
	                	// 'required'=>true
	                ]
	            ) ?>
                <span class="input-group-btn"></span>
	            <span class="input-group-btn">
	                <button class="btn btn-success field-create-sopenport" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
	            </span>
	        </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var sopenPortMax = 0', []); ?>
    <?= $form->beginField(new SOpenPort, '[0]sopenports'); ?>
        <label class="control-label" for="sopenport-sopenports">Порты, прослушиваемые на оборудовании</label>
        <div class="input-group">
            <?= Html::input(
                'text',
                'SOpenPort[0][port_number]',
                '',
                [
                	'class' => 'form-control',
                	'placeholder'=>'Номер порта',
                	// 'required'=>true
                ]
            ) ?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::DropDownList(
                'SOpenPort[0][protocol]',
                false,
                $model->indexedProtocols,
                [
                    'prompt'=>'Протокол: не выбран',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'text',
                'SOpenPort[0][description]',
                '',
                [
                	'class' => 'form-control',
                	'placeholder'=>'Описание',
                	'maxlength'=>'255',
                	// 'required'=>true
                ]
            ) ?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-sopenport" type="button">+</button>
                <button class="btn btn-danger field-remove disabled" type="button">x</button>
            </span>
        </div>
        <div class="help-block"></div>
    <?= $form->endField(); ?>
<?php endif ?>
<?php
// далее скрипты на JS
$search = <<< JS
    // добавление блоков
    jQuery(document).on('click', '.field-create-sopenport',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        sopenPortMax++;
        newel.attr('class', 'form-group field-sopenport-'+(sopenPortMax)+'customgroup' );
        newel.find('select[name *= "protocol"]').attr({
            'name': 'SOpenPort['+(sopenPortMax)+'][protocol]',
            'id': 'sopenport-'+(sopenPortMax)+'-protocol'
        });
		newel.find('input[name *= "port_number"]').attr('name', 'SOpenPort['+(sopenPortMax)+'][port_number]');
		newel.find('input[name *= "port_number"]').val('');
		newel.find('input[name *= "description"]').attr('name', 'SOpenPort['+(sopenPortMax)+'][description]');
		newel.find('input[name *= "description"]').val('');
        newel.find('button').removeClass('disabled');
    });

JS;
$this->registerJs($search, View::POS_READY);