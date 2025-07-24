<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\reference\SOrganization;
use app\models\reference\SContractor;

function getListOrganization(){
    return  ArrayHelper::map(SOrganization::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name');
}
$listOrganization = getListOrganization();

$contr = SContractor::find()->where(['contract' => $model->id, 'prime' => null])->One();
?>

<?= $form->beginField(new SContractor, 'PrimarySContractor'); ?>
    <label class="control-label" for="PrimarySContractor">Исполнитель по контракту, подрядчик</label>
    <div class="input-group">
        <?= Html::DropDownList(
            'PrimarySContractor',
            $contr ? $contr->organization : false,
            $listOrganization,
            [
                'prompt'=>'Выберите элемент',
                'class'=>'form-control',
                'style'=>'z-index:0'
            ]
        )?>
        <span class="input-group-btn">
            <?= Html::a('Добавить новую организацию', ['refs/create', 'refid'=>'SOrganization'], ['class' => 'btn btn-success', 'target'=>'_blank']) ?>
        </span>
    </div>
    <div class="help-block"></div>
<?= $form->endField(); ?>