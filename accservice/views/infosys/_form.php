<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\reference\CVariantValidation;
use app\models\reference\CLevelProtection;
use app\models\reference\CLevelPrivacy;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RInformationSystem */
/* @var $form yii\widgets\ActiveForm */
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Информационная система успешно добавлена' ?></h4>
            </div>
            <!-- <div class="modal-body">
            </div> -->
            <div class="modal-footer">
                <?= Html::a('Перейти', ['infosys/view', 'id' => isset($switcherId) ? $switcherId : ''], ['class' => 'btn btn-primary'])?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Добавить новую</button>
            </div>
        </div>
    </div>
</div>

<?php
$refrashUrl = Yii::$app->urlManager->createUrl(['infosys/create']);
$this->registerJs("
$('#myModal').on('hidden.bs.modal', function (e) {
    window.location.replace('{$refrashUrl}');
})
", $this::POS_READY);
if(!empty($showModalSwitcher))
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
/**/
?>

<div class="rinformation-system-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name_short')->textInput(['maxlength' => 128])->label('Краткое наименование (неофициальное)') ?>

    <?= $form->field($model, 'name_full')->textInput(['maxlength' => 1024])->label('Полное наименование (официальное)') ?>

    <?= $form->field($model, 'description')->textArea(['maxlength' => 2048]) ?>

    <?php // контракты ?>
    <?php echo $this->render('_form/_block_rcontract', ['model' => $model, 'form' => $form]); ?>

    <?php // нпа ?>
    <?php echo $this->render('_form/_block_rlegaldoc', ['model' => $model, 'form' => $form]); ?>

    <?php // ответственные лица ?>
    <?php echo $this->render('_form/_block_sresponseis', ['model' => $model, 'form' => $form]); ?>

    <?=
        $form->field($model, 'validation')
            ->dropDownList(
                $tempRes = ArrayHelper::map(
                    CVariantValidation::find()
                        ->select('id, name')
                        ->asArray()
                        ->All(), 'id', 'name'),
                    ['prompt'=>'Выберите элемент']
            )
            ->label('Аттестация системы')
    ?>

    <?=
        $form->field($model, 'protection')
            ->dropDownList(
                $tempRes = ArrayHelper::map(
                    CLevelProtection::find()
                        ->select('id, name')
                        ->asArray()
                        ->All(), 'id', 'name'),
                    ['prompt'=>'Выберите элемент'])
    ?>

    <?=
        $form->field($model, 'privacy')
            ->dropDownList(
                $tempRes = ArrayHelper::map(
                    CLevelPrivacy::find()
                        ->select('id, name')
                        ->asArray()
                        ->All(), 'id', 'name'),
                    ['prompt'=>'Выберите элемент'])
    ?>

    <?php // дистрибутивы ПО ?>
    <?php echo $this->render('_form/_block_rsoftware', ['model' => $model, 'form' => $form]); ?>

    <?php // установленное ПО ?>
    <?php echo $this->render('_form/_block_rsoftinstalled', ['model' => $model, 'form' => $form]); ?>

    <?php // документация ?>
    <?php echo $this->render('_form/_block_rdocumentation', ['model' => $model, 'form' => $form]); ?>

    <?php
    // Пароли
    if ($model->showPassInputInForm) {
        echo $this->render('_form/_block_rpassword', ['model' => $model, 'form' => $form]);
    }
    ?>

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
$this->registerJs($search, \yii\web\View::POS_READY);