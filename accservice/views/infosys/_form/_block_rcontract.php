<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnIsContract;
use app\models\registry\RContract;
?>

<?php if ($arrNnIsContract = $model->nnIsContracts): ?>
    <?= Html::script('var contractMax = '.(count($arrNnIsContract)-1), []); ?>
    <?php foreach ($arrNnIsContract as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnIsContract, "[{$keyv}]nniscontracts"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nniscontract-nniscontracts">Связи с контрактами</label>
            <?php endif ?>
	        <div class="input-group">
	            <?= Html::DropDownList(
	                "NnIsContract[{$keyv}][contract]",
	                $v->contract0->id,
	                ArrayHelper::map(
	                    RContract::find()
	                        ->select('id, name')
                            ->orderBy('name')
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
	                <button class="btn btn-success field-create-contract" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
	            </span>
	        </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var contractMax = 0', []); ?>
    <?= $form->beginField(new NnIsContract, '[0]nniscontracts'); ?>
        <label class="control-label" for="nniscontract-nniscontracts">Связи с контрактами</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnIsContract[0][contract]',
                false,
                ArrayHelper::map(
                    RContract::find()
                        ->select('id, name')
                        ->orderBy('name')
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
                <button class="btn btn-success field-create-contract" type="button">+</button>
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
    easyInputManage('contract','contractMax','NnIsContract');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);