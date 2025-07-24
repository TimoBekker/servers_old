<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnLdocIs;
use app\models\registry\RLegalDoc;
?>

<?php if ($arrNnLdocIs = $model->nnLdocIs): ?>
    <?= Html::script('var legaldocMax = '.(count($arrNnLdocIs)-1), []); ?>
    <?php foreach ($arrNnLdocIs as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnLdocIs, "[{$keyv}]nnldocis"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nnldocis-nnldocis">Связи с нормативно-правовыми актами</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::DropDownList(
                    "NnLdocIs[{$keyv}][legal_doc]",
                    $v->legalDoc->id,
                    ArrayHelper::map(
                    RLegalDoc::find()
                        ->select(['r_legal_doc.id', 'concat(
                                                        ctld.name, ", ",
                                                        if(r_legal_doc.date, concat(date_format(`r_legal_doc`.`date`, "%d.%m.%Y"),", "), ""),
                                                        r_legal_doc.name
                                                    ) as name'])
                        ->join('INNER JOIN', 'c_type_legal_doc ctld', 'ctld.id = r_legal_doc.type')
                        ->orderBy('ctld.name, r_legal_doc.date, r_legal_doc.name')
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
                    <button class="btn btn-success field-create-legal_doc" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var legaldocMax = 0', []); ?>
    <?= $form->beginField(new NnLdocIs, '[0]nnldocis'); ?>
        <label class="control-label" for="nnldocis-nnldocis">Связи с нормативно-правовыми актами</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnLdocIs[0][legal_doc]',
                false,
                ArrayHelper::map(
                    RLegalDoc::find()
                        ->select(['r_legal_doc.id', 'concat(
                                                        ctld.name, ", ",
                                                        if(r_legal_doc.date, concat(date_format(`r_legal_doc`.`date`, "%d.%m.%Y"),", "), ""),
                                                        r_legal_doc.name
                                                    ) as name'])
                        ->join('INNER JOIN', 'c_type_legal_doc ctld', 'ctld.id = r_legal_doc.type')
                        ->orderBy('ctld.name, r_legal_doc.date, r_legal_doc.name')
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
                <button class="btn btn-success field-create-legal_doc" type="button">+</button>
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
    easyInputManage('legal_doc','legaldocMax','NnLdocIs');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);