<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\reference\SResponseWarrantySupport;

function getListPersons(){
    return  ArrayHelper::map(
        User::find()
            ->select(['user.id as id', 'concat(
                                            user.last_name, " ",
                                            user.first_name, " ",
                                            user.second_name, " (",
                                            so.name, " )"
                                        ) as name'])
            ->join('INNER JOIN', 's_organization so', 'so.id = user.organization')
            ->orderBy('user.last_name, user.first_name, user.second_name')
            ->asArray()
            ->All(), 'id', 'name'
    );
}
$listPersons = getListPersons();

?>

<?php if ($arrSResponseWarrantySupport = $model->sResponseWarrantySupports): ?>
    <?= Html::script('var responsewarrantyMax = '.(count($arrSResponseWarrantySupport)-1), []); ?>
    <?php foreach ($arrSResponseWarrantySupport as $keyv => $v): ?>
        <?php // var_dump($v->); ?>
        <?= $form->beginField(new SResponseWarrantySupport, "[{$keyv}]sResponseWarrantySupports"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="SResponseWarrantySupport-sResponseWarrantySupports">Ответственные за гарантийную поддержку</label>
            <?php endif ?>
        <div class="input-group">
            <?= Html::DropDownList(
                "SResponseWarrantySupport[{$keyv}][response_person]",
                !is_null($v->responsePerson) ? $v->responsePerson->id : false,
				$listPersons,
                [
                    'prompt'=>'Ответственное лицо: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-response_person" type="button">+</button>
                <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
            </span>
        </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>
    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var responsewarrantyMax = 0', []); ?>
    <?= $form->beginField(new SResponseWarrantySupport, '[0]sResponseWarrantySupports'); ?>
        <label class="control-label" for="SResponseWarrantySupport-sResponseWarrantySupports">Ответственные за гарантийную поддержку</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'SResponseWarrantySupport[0][response_person]',
                false,
				$listPersons,
                [
                    'prompt'=>'Ответственное лицо: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-response_person" type="button">+</button>
                <button class="btn btn-danger field-remove disabled" type="button">x</button>
            </span>
        </div>
        <div class="help-block"></div>
    <?= $form->endField(); ?>
<?php endif ?>
<div class="form-group">
    <?= Html::a('Добавить новое ответственное лицо', ['user/create'], ['class' => 'btn btn-success center-block', 'target'=>'_blank']) ?>
</div>
<?php
// далее скрипты на JS
$search = <<< JS
easyInputManage('response_person','responsewarrantyMax','SResponseWarrantySupport');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);