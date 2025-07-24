<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\registry\RContract;
use app\models\relation\NnLdocContract;

function getListContracts(){
    return  ArrayHelper::map(
        RContract::find()
            ->select('id, name')
            ->orderBy('name')
            ->asArray()
            ->All(), 'id', 'name'
    );
}
$listContracts = getListContracts();

?>

<?php if ($arrNnLdocContract = $model->nnLdocContracts): ?>
    <?= Html::script('var rcontractMax = '.(count($arrNnLdocContract)-1), []); ?>
    <?php foreach ($arrNnLdocContract as $keyv => $v): ?>
        <?php // var_dump($v->); ?>
        <?= $form->beginField(new NnLdocContract, "[{$keyv}]nnLdocContracts"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="NnLdocContract-nnLdocContracts">Связь с контрактами</label>
            <?php endif ?>
        <div class="input-group">
            <?= Html::DropDownList(
                "NnLdocContract[{$keyv}][contract]",
                $v->contract0->id,
				$listContracts,
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
    <?= Html::script('var rcontractMax = 0', []); ?>
    <?= $form->beginField(new NnLdocContract, '[0]nnLdocContracts'); ?>
        <label class="control-label" for="NnLdocContract-nnLdocContracts">Связь с контрактами</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnLdocContract[0][contract]',
                false,
				$listContracts,
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
easyInputManage('contract','rcontractMax','NnLdocContract');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);