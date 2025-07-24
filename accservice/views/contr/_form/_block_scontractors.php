<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\reference\SOrganization;
use app\models\reference\SContractor;

/*function getListOrganization(){
    return  ArrayHelper::map(SOrganization::find()->select('id, name')->asArray()->All(), 'id', 'name');
}*/
$listOrganization = getListOrganization(); // Объявлена в _block_scontractor

$contr = SContractor::find()->select('id')->where(['contract' => $model->id, 'prime' => null])->One();

?>

<?php // if ($arrSContractor = $model->sContractors): Здесь так не пойдет. Нам надо доставать всех без генподрядчика?>
<?php if ($arrSContractor = SContractor::find()->where([ 'and', ['contract' => $model->id], ['not', ['prime' => null]] ])->All()): ?>
    <?= Html::script('var contractorsMax = '.(count($arrSContractor)-1), []); ?>
    <?php foreach ($arrSContractor as $keyv => $v): ?>

        <?= $form->beginField(new SContractor, "[{$keyv}]sContractors"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="SContractor-sContractors">Исполнитель по контракту, субподрядчик</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::DropDownList(
                    "SContractor[{$keyv}][organization]",
                    $v->organization0->id,
                    $listOrganization,
                    [
                        'prompt'=>'Выберите элемент',
                        'class'=>'form-control',
                        'style'=>'z-index:0'
                    ]
                )?>
                <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-organization" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var contractorsMax = 0', []); ?>
    <?= $form->beginField(new SContractor, '[0]sContractors'); ?>
        <label class="control-label" for="SContractor-sContractors">Исполнитель по контракту, субподрядчик</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'SContractor[0][organization]',
                false,
                $listOrganization,
                [
                    'prompt'=>'Выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-organization" type="button">+</button>
                <button class="btn btn-danger field-remove disabled" type="button">x</button>
            </span>
        </div>
        <div class="help-block"></div>
    <?= $form->endField(); ?>
<?php endif ?>
<div class="form-group">
    <?= Html::a('Добавить новую организацию', ['refs/create', 'refid'=>'SOrganization'], ['class' => 'btn btn-success center-block', 'target'=>'_blank']) ?>
</div>
<?php
// далее скрипты на JS
$search = <<< JS
    // контракты
    easyInputManage('organization','contractorsMax','SContractor');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);