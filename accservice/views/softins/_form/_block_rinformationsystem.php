<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnIsSoftinstall;
use app\models\registry\RInformationSystem;
?>

<?php if ($arrNnIsSoftinstall = $model->nnIsSoftinstalls): ?>
    <?= Html::script('var infosystemMax = '.(count($arrNnIsSoftinstall)-1), []); ?>
    <?php foreach ($arrNnIsSoftinstall as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnIsSoftinstall, "[{$keyv}]nnissoftinstalls"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nnissoftinstall-nnissoftinstalls">Связать с информационной системой</label>
            <?php endif ?>
	        <div class="input-group">
	            <?= Html::DropDownList(
	                "NnIsSoftinstall[{$keyv}][information_system]",
	                $v->informationSystem->id,
	                ArrayHelper::map(
	                    RInformationSystem::find()
	                        ->select(['id', 'name_short'])
                            ->orderBy('name_short')
	                        ->asArray()
	                        ->All(), 'id', 'name_short'
	                ),
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
    <?= $form->beginField(new NnIsSoftinstall, '[0]nnissoftinstalls'); ?>
        <label class="control-label" for="nnissoftinstall-nnissoftinstalls">Связать с информационной системой</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnIsSoftinstall[0][information_system]',
                false,
                ArrayHelper::map(
                    RInformationSystem::find()
                            ->select(['id', 'name_short'])
                            ->orderBy('name_short')
                            ->asArray()
                            ->All(), 'id', 'name_short'
                ),
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
    easyInputManage('information_system','infosystemMax','NnIsSoftinstall');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);