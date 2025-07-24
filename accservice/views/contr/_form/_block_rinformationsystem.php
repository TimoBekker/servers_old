<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnIsContract;
use app\models\registry\RInformationSystem;

function getListInfosys(){
    return  ArrayHelper::map(
        RInformationSystem::find()
            ->select(['id', 'name_short'])
            ->orderBy('name_short')
            ->asArray()
            ->All(), 'id', 'name_short'
    );
}
$listInfosys = getListInfosys();

?>

<?php if ($arrNnIsContract = $model->nnIsContracts): ?>
    <?= Html::script('var infosystemMax = '.(count($arrNnIsContract)-1), []); ?>
    <?php foreach ($arrNnIsContract as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnIsContract, "[{$keyv}]nnIsContracts"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="NnIsContract-nnIsContracts">Связать с информационными системами</label>
            <?php endif ?>
	        <div class="input-group">
	            <?= Html::DropDownList(
	                "NnIsContract[{$keyv}][information_system]",
	                $v->informationSystem->id,
                    $listInfosys,
	                [
	                    'prompt'=>'Выберите элемент',
	                    'class'=>'form-control',
	                    'style'=>'z-index:0'
	                ]
	            )?>
                <span class="input-group-btn"></span>
	            <span class="input-group-btn">
	                <button class="btn btn-success field-create-information_system" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
	            </span>
	        </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var infosystemMax = 0', []); ?>
    <?= $form->beginField(new NnIsContract, '[0]nnIsContracts'); ?>
        <label class="control-label" for="NnIsContract-nnIsContracts">Связать с информационными системами</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnIsContract[0][information_system]',
                false,
                $listInfosys,
                [
                    'prompt'=>'Выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-information_system" type="button">+</button>
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
    easyInputManage('information_system','infosystemMax','NnIsContract');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);