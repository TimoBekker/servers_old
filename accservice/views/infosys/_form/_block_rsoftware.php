<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnIsSoftware;
use app\models\registry\RSoftware;
?>

<?php if ($arrNnIsSoftware = $model->nnIsSoftwares): ?>
    <?= Html::script('var softwareMax = '.(count($arrNnIsSoftware)-1), []); ?>
    <?php foreach ($arrNnIsSoftware as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnIsSoftware, "[{$keyv}]nnissoftwares"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nnissoftware-nnissoftwares">Состоит из следующих компонентов ПО</label>
            <?php endif ?>
	        <div class="input-group">
	            <?= Html::DropDownList(
	                "NnIsSoftware[{$keyv}][software]",
	                $v->software0->id,
	                ArrayHelper::map(
	                    RSoftware::find()
	                        ->select(['r_software.id', 'concat(
	                                                        cts.name, ", ",
	                                                        r_software.name, " ",
	                                                        r_software.version
	                                                    ) as name'])
	                        ->join('INNER JOIN', 'c_type_software cts', 'cts.id = r_software.type')
                            ->orderBy('cts.name, r_software.name, r_software.version')
	                        ->asArray()
	                        ->All(), 'id', 'name'
	                ),
	                [
	                    'prompt'=>'Выберите элемент',
	                    'class'=>'form-control',
	                    'style'=>'z-index:0'
	                ]
	            )?>
                <span class="input-group-btn"></span>
	            <span class="input-group-btn">
	                <button class="btn btn-success field-create-software" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
	            </span>
	        </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var softwareMax = 0', []); ?>
    <?= $form->beginField(new NnIsSoftware, '[0]nnissoftwares'); ?>
        <label class="control-label" for="nnissoftware-nnissoftwares">Состоит из следующих компонентов ПО</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnIsSoftware[0][software]',
                false,
                ArrayHelper::map(
                    RSoftware::find()
                        ->select(['r_software.id', 'concat(
                                                        cts.name, ", ",
                                                        r_software.name, " ",
                                                        r_software.version
                                                    ) as name'])
                        ->join('INNER JOIN', 'c_type_software cts', 'cts.id = r_software.type')
                        ->orderBy('cts.name, r_software.name, r_software.version')
                        ->asArray()
                        ->All(), 'id', 'name'
                ),
                [
                    'prompt'=>'Выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-software" type="button">+</button>
                <button class="btn btn-danger field-remove disabled" type="button">x</button>
            </span>
        </div>
        <div class="help-block"></div>
    <?= $form->endField(); ?>
<?php endif ?>
<?php
// далее скрипты на JS
$search = <<< JS
    // контракты
    easyInputManage('software','softwareMax','NnIsSoftware');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);