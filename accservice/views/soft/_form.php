<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\reference\CTypeSoftware;
use app\models\reference\CTypeLicense;
use app\models\reference\SOrganization;
use app\models\reference\CMethodLicense;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftware */
/* @var $form yii\widgets\ActiveForm */
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Дистрибутив успешно добавлен' ?></h4>
            </div>
            <!-- <div class="modal-body">
            </div> -->
            <div class="modal-footer">
                <?= Html::a('Перейти', ['soft/view', 'id' => isset($switcherId) ? $switcherId : ''], ['class' => 'btn btn-primary'])?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Добавить новый</button>
            </div>
        </div>
    </div>
</div>

<?php
$refrashUrl = Yii::$app->urlManager->createUrl(['soft/create']);
$this->registerJs("
$('#myModal').on('hidden.bs.modal', function (e) {
    window.location.replace('{$refrashUrl}');
})
", $this::POS_READY);
if(!empty($showModalSwitcher))
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
/**/
?>

<div class="rsoftware-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 512]) ?>

    <?=
        $form->field($model, 'type')->dropDownList(
            ArrayHelper::map(
                CTypeSoftware::find()->select('id, name')->asArray()->All(), 'id', 'name'),
                ['prompt'=>'Выберите элемент'])
    ?>

    <?= $form->field($model, 'version')->textInput(['maxlength' => 255])->label('Версия ПО') ?>

    <?= $form->field($model, 'description')->textArea(['maxlength' => 2048])->label('Описание ПО') ?>

    <?=
        $form->field($model, 'type_license')->dropDownList(
            ArrayHelper::map(CTypeLicense::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ['prompt'=>'Выберите элемент'])->label('Тип лицензии (Free / OpenSource / …)')
    ?>

    <?= $form->field($model, 'count_license')->textInput(['maxlength' => 5, 'placeholder' => 'Чтобы задать бесконечное количество введите 0'])->label('Количество доступных лицензий') ?>

    <?=
        $form->field($model, 'date_end_license')->widget(\yii\jui\DatePicker::classname(), [
            'language' => 'ru-Ru',
            // 'dateFormat' => 'yyyy-MM-dd',
            'dateFormat' => 'dd.MM.yyyy',
            'options' => [
                'class' => 'form-control',
            ]
        ])->label('Дата окончания действия лицензии')
    ?>

    <?= $form->beginField($model, 'owner'); ?>
        <?= Html::Label('Организация-владелец лицензии'); ?>
        <div class="input-group">
            <?=
                Html::activeDropDownList(
                    $model, 'owner', $tempRes = ArrayHelper::map(
                        SOrganization::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name'),
                        ['prompt'=>'Выберите элемент', 'class' => 'form-control', 'style'=>'z-index:0']
                )
            ?>
            <span class="input-group-btn">
                <?= Html::a('Добавить новую организацию', ['refs/create', 'refid'=>'SOrganization'], ['class' => 'btn btn-success', 'target'=>'_blank']) ?>
            </span>
        </div>
    <?= $form->endField() ?>

    <?=
        $form->field($model, 'method_license')->dropDownList(
            $tempRes = ArrayHelper::map(
                CMethodLicense::find()->select('id, name')->asArray()->All(), 'id', 'name'),
                ['prompt'=>'Выберите элемент']
            )->label('Способ лицензирования (по CPU / пользователям / …)')
    ?>

    <?= $form->beginField($model, 'developer'); ?>
        <?= Html::activeLabel($model, 'developer'); ?>
        <div class="input-group">
            <?=
                Html::activeDropDownList(
                    $model, 'developer', $tempRes = ArrayHelper::map(
                        SOrganization::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name'),
                    ['prompt'=>'Выберите элемент', 'class' => 'form-control', 'style'=>'z-index:0']
                )
            ?>
            <span class="input-group-btn">
                <?= Html::a('Добавить новую организацию', ['refs/create', 'refid'=>'SOrganization'], ['class' => 'btn btn-success', 'target'=>'_blank']) ?>
            </span>
        </div>
    <?= $form->endField() ?>

    <?= $form->field($model, 'count_cores')->textInput(['maxlength' => 5])->label('Требуемое количество ядер CPU') ?>

    <?= $form->field($model, 'amount_ram')->textInput(['maxlength' => 8, 'placeholder'=>'В мегабайтах', 'value' => $model->amount_ram ? $model->amount_ram * 10 : ''])->label('Требуемый объем ОЗУ') ?>

    <?= $form->field($model, 'volume_hdd')->textInput(['maxlength' => 8, 'placeholder'=>'В гигабайтах', 'value' => $model->volume_hdd ? $model->volume_hdd / 10 : ''])->label('Требуемый объем HDD') ?>

    <?= $form->field($model, 'inventory_number')->textInput(['maxlength' => 128]) ?>

    <?php // контракты ?>
    <?php echo $this->render('_form/_block_rcontract', ['model' => $model, 'form' => $form]); ?>

    <?php // взаимосвязь с другими дистрибутивами ?>
    <?php echo $this->render('_form/_block_rsoft', ['model' => $model, 'form' => $form]); ?>

    <?php // связь с информационными системами ?>
    <?php echo $this->render('_form/_block_rsoftware', ['model' => $model, 'form' => $form]); ?>

    <?php // документация ?>
    <?php echo $this->render('_form/_block_rdocumentation', ['model' => $model, 'form' => $form]); ?>

    <?php // Группы доступа ?>
    <?php echo $this->render('_form/_block_usergroups', ['model' => $model, 'form' => $form, 'isCreateScena' => $isCreateScena]); ?>

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
$this->registerJs($search,  \yii\web\View::POS_READY);
