<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;
use app\models\reference\CVariantResponsibility;
use app\models\reference\SResponseInformationSystem;
use app\models\registry\RLegalDoc;
?>

<?php if ($arrSResponseInformationSystem = $model->sResponseInformationSystems): ?>
    <?= Html::script('var responseinformationsystemMax = '.(count($arrSResponseInformationSystem)-1), []); ?>
    <?php foreach ($arrSResponseInformationSystem as $keyv => $v): ?>
        <?php // var_dump($v->); ?>
        <?= $form->beginField(new SResponseInformationSystem, "[{$keyv}]sResponseInformationSystems"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="sresponseinformationsystem-sResponseInformationSystems">Ответственные лица</label>
            <?php endif ?>
        <div class="input-group">
            <?= Html::DropDownList(
                "SResponseInformationSystem[{$keyv}][response_person]",
                !is_null($v->responsePerson) ? $v->responsePerson->id : false,
                ArrayHelper::map(
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
                ),
                [
                    'prompt'=>'Ответственное лицо: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::DropDownList(
                "SResponseInformationSystem[{$keyv}][responsibility]",
                !is_null($v->responsibility0) ? $v->responsibility0->id : false,
                ArrayHelper::map(
                    CVariantResponsibility::find()
                        ->select('id, name')
                        ->orderBy('name')
                        ->asArray()
                        ->All(), 'id', 'name'
                ),
                [
                    'prompt'=>'Вид ответственности: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::DropDownList(
                "SResponseInformationSystem[{$keyv}][legal_doc]",
                !is_null($v->legalDoc) ? $v->legalDoc->id : false,
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
                    'prompt'=>'Ссылка на приказ: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-sresponseinformationsystem" type="button">+</button>
                <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
            </span>
        </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>
    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var responseinformationsystemMax = 0', []); ?>
    <?= $form->beginField(new SResponseInformationSystem, '[0]sResponseInformationSystems'); ?>
        <label class="control-label" for="sresponseinformationsystem-sResponseInformationSystems">Ответственные лица</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'SResponseInformationSystem[0][response_person]',
                false,
                ArrayHelper::map(
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
                ),
                [
                    'prompt'=>'Ответственное лицо: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::DropDownList(
                'SResponseInformationSystem[0][responsibility]',
                false,
                ArrayHelper::map(
                    CVariantResponsibility::find()
                        ->select('id, name')
                        ->orderBy('name')
                        ->asArray()
                        ->All(), 'id', 'name'
                ),
                [
                    'prompt'=>'Вид ответственности: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::DropDownList(
                'SResponseInformationSystem[0][legal_doc]',
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
                    'prompt'=>'Ссылка на приказ: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-sresponseinformationsystem" type="button">+</button>
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
    //отвественные лица---этот блок почти по феншую
    jQuery(document).on('click', '.field-create-sresponseinformationsystem',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        responseinformationsystemMax++;
        newel.attr('class', 'form-group field-sresponseinformationsystem-'+(responseinformationsystemMax)+'customgroup' );
        newel.find('select[name *= "response_person"]').attr({
            'name': 'SResponseInformationSystem['+(responseinformationsystemMax)+'][response_person]',
            'id': 'sresponseinformationsystem-'+(responseinformationsystemMax)+'-response_person'
        });
        newel.find('select[name *= "responsibility"]').attr({
            'name': 'SResponseInformationSystem['+(responseinformationsystemMax)+'][responsibility]',
            'id': 'sresponseinformationsystem-'+(responseinformationsystemMax)+'-responsibility'
        });
        newel.find('select[name *= "legal_doc"]').attr({
            'name': 'SResponseInformationSystem['+(responseinformationsystemMax)+'][legal_doc]',
            'id': 'sresponseinformationsystem-'+(responseinformationsystemMax)+'-legal_doc'
        });
        newel.find('button').removeClass('disabled');
    });
JS;
$this->registerJs($search, \yii\web\View::POS_READY);