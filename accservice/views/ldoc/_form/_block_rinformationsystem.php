<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnLdocIs;
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

<?php if ($arrNnLdocIs = $model->nnLdocIs): ?>
    <?= Html::script('var infosystemMax = '.(count($arrNnLdocIs)-1), []); ?>
    <?php foreach ($arrNnLdocIs as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnLdocIs, "[{$keyv}]nnLdocIs"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="NnLdocIs-nnLdocIs">Связать с информационными системами</label>
            <?php endif ?>
	        <div class="input-group">
	            <?= Html::DropDownList(
	                "NnLdocIs[{$keyv}][information_system]",
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
    <?= $form->beginField(new NnLdocIs, '[0]nnLdocIs'); ?>
        <label class="control-label" for="NnLdocIs-nnLdocIs">Связать с информационными системами</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnLdocIs[0][information_system]',
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
    easyInputManage('information_system','infosystemMax','NnLdocIs');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);