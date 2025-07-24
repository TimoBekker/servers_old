<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\registry\RLegalDoc;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\models\relation\NnLdocContract;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RContract */
/* @var $form yii\widgets\ActiveForm */
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Контракт успешно добавлен' ?></h4>
            </div>
            <!-- <div class="modal-body">
            </div> -->
            <div class="modal-footer">
                <?= Html::a('Перейти', ['contr/view', 'id' => isset($switcherId) ? $switcherId : ''], ['class' => 'btn btn-primary'])?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Добавить новый</button>
            </div>
        </div>
    </div>
</div>

<?php
$refrashUrl = Yii::$app->urlManager->createUrl(['contr/create']);
$this->registerJs("
$('#myModal').on('hidden.bs.modal', function (e) {
    window.location.replace('{$refrashUrl}');
})
", $this::POS_READY);
if(!empty($showModalSwitcher))
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
/**/
?>

<div class="rcontract-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 512]) ?>
 <?= $form->field($model, 'contract_subject')->textInput(['maxlength' => 512]) ?>

    <?= $form->field($model, 'requisite')->textInput(['maxlength' => 512]) ?>

    <?= $form->field($model, 'cost')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'date_complete')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru-Ru',
        // 'dateFormat' => 'yyyy-MM-dd',
        'dateFormat' => 'dd.MM.yyyy',
        'options' => [
            'class' => 'form-control',
        ]
    ]) ?>

    <?php // Подрядчик ?>
    <?php echo $this->render('_form/_block_scontractor', ['model' => $model, 'form' => $form]); ?>

    <?php // Субподрядчики ?>
    <?php echo $this->render('_form/_block_scontractors', ['model' => $model, 'form' => $form]); ?>

    <?= $form->field($model, 'date_begin_warranty')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru-Ru',
        // 'dateFormat' => 'yyyy-MM-dd',
        'dateFormat' => 'dd.MM.yyyy',
        'options' => [
            'class' => 'form-control',
        ]
    ]) ?>

    <?= $form->field($model, 'date_end_warranty')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru-Ru',
        // 'dateFormat' => 'yyyy-MM-dd',
        'dateFormat' => 'dd.MM.yyyy',
        'options' => [
            'class' => 'form-control',
        ]
    ]) ?>


    <?php // ответственные за гарантийную поддержку ?>
    <?php echo $this->render('_form/_block_sresponsewarrantysupport', ['model' => $model, 'form' => $form]); ?>


    <?php // Связь с НПА многие-ко-многим ?>
    <?php if ($arrContracts = $model->nnLdocContracts): ?>
        <?= Html::script('var legalDocMax = '.(count($arrContracts)-1), []); ?>
        <?php foreach ($arrContracts as $keyv => $v): ?>

            <?= $form->beginField(new NnLdocContract, "[{$keyv}]legal_doc"); ?>
                <?php if ($keyv == 0): ?>
                    <label class="control-label" for="nnldoccontract-legal_doc">Связь с нормативно-правовыми актами</label>
                <?php endif ?>
                <div class="input-group">
                    <?= Html::DropDownList(
                        // new NnLdocContract,
                        "NnLdocContract[{$keyv}][legal_doc]",
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
                            // 'id'=>'nnldoccontract-'.$keyv.'-legal_doc',
                            'prompt'=>'Выберите элемент',
                            'class'=>'form-control',
                            'style'=>'z-index:0'
                        ]
                    ) ?>
                    <span class="input-group-btn"></span>
                    <span class="input-group-btn">
                        <button id="add-npa-input" class="btn btn-success field-create" type="button">+</button>
                        <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                    </span>
                </div>
                <div class="help-block"></div>
            <?= $form->endField(); ?>

        <?php endforeach ?>
    <?php else: ?>
        <?= Html::script('var legalDocMax = 0', []); ?>
        <?= $form->beginField(new NnLdocContract, '[0]legal_doc'); ?>
            <label class="control-label" for="nnldoccontract-legal_doc">Связь с нормативно-правовыми актами</label>
            <div class="input-group">
                <?= Html::DropDownList(
                    "NnLdocContract[0][legal_doc]",
                    false,
                    ArrayHelper::map(RLegalDoc::find()
                            ->select(['r_legal_doc.id', 'concat(
                                                            ctld.name, ", ",
                                                            if(r_legal_doc.date, concat(date_format(`r_legal_doc`.`date`, "%d.%m.%Y"),", "), ""),
                                                            r_legal_doc.name
                                                        ) as name'])
                            ->join('INNER JOIN', 'c_type_legal_doc ctld', 'ctld.id = r_legal_doc.type')
                            ->orderBy('ctld.name, r_legal_doc.date, r_legal_doc.name')
                            ->asArray()
                            ->All(), 'id', 'name'
                    ), ['prompt'=>'Выберите элемент', 'class'=>'form-control', 'style'=>'z-index:0']) ?>
                    <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button id="add-npa-input" class="btn btn-success field-create" type="button">+</button>
                    <button class="btn btn-danger field-remove disabled" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>
    <?php endif ?>

    <?php // Информационные системы ?>
    <?php echo $this->render('_form/_block_rinformationsystem', ['model' => $model, 'form' => $form]); ?>

    <?php // Дистрибутивы ПО ?>
    <?php echo $this->render('_form/_block_rsoftware', ['model' => $model, 'form' => $form]); ?>

    <?php // = $form->field($model, 'url')->fileInput(['class'=>'form-control'])->label('Ссылка на файл с контрактом') ?>
    <?= $form->beginField($model, 'url'); ?>
        <?= Html::label('Ссылка на файл с контрактом') ?>
        <div class="input-group">
        <?= Html::activeFileInput($model, 'url', ['class'=>'form-control']) ?>
        <?php if ($model->url): ?>
            <span class="input-group-btn"></span>
            <?= Html::a('Текущий: '.basename($model->url), "@web/{$model->url}", ['class'=>'form-control']) ?>
            <?= Html::Input('hidden', 'currenturl', $model->url, []) ?>
        <?php endif; ?>
        </div>
    <?= $form->endField(); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
// далее скрипты на JS
$search = <<< JS
    // для динамически добавляемых полей используем делегирование через on
    jQuery(document).on('click', '.field-remove',function(event) {
        $(this).parent().parent().parent().remove();
        // alert('test');
        event.preventDefault();
    });
    jQuery(document).on('click', '.field-create',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        legalDocMax++;
        var ninputClass =
            'form-group field-nnldoccontract-'+(legalDocMax)+'-legal_doc required';
        newel.attr('class', ninputClass);
        var ninputName = 'NnLdocContract['+(legalDocMax)+'][legal_doc]';
        newel.find('select').attr('name', ninputName);
        var ninputId = 'nnldoccontract-'+(legalDocMax)+'-legal_doc';
        newel.find('select').attr('id', ninputId);
        newel.find('button').removeClass('disabled');
    });
    function easyInputManage(elEnty, varMaxName, relationName){
        jQuery(document).on('click', '.field-create-'+elEnty, function(event) {
            var newel;
            $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
            newel.children('label').remove();
            window[varMaxName]++;
            var ninputClass =
                'form-group field-'+relationName.toLowerCase()+'-'+(window[varMaxName])+'-'+elEnty+' required';
            newel.attr('class', ninputClass);
            var ninputName = relationName+'['+(window[varMaxName])+']['+elEnty+']';
            newel.find('select').attr('name', ninputName);
            var ninputId = relationName.toLowerCase()+'-'+(window[varMaxName])+'-'+elEnty;
            newel.find('select').attr('id', ninputId);
            newel.find('button').removeClass('disabled');
        });
    }
JS;
    $this->registerJs($search, View::POS_READY);
    // where $position can be View::POS_READY (the default),
    // or View::POS_HEAD, View::POS_BEGIN, View::POS_END
