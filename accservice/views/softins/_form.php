<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\registry\RSoftware;
use app\models\registry\REquipment;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RSoftwareInstalled */
/* @var $form yii\widgets\ActiveForm */
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Установленное ПО успешно учтено' ?></h4>
            </div>
            <!-- <div class="modal-body">
            </div> -->
            <div class="modal-footer">
                <?= Html::a('Перейти', ['softins/view', 'id' => isset($switcherId) ? $switcherId : ''], ['class' => 'btn btn-primary'])?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Учесть новое</button>
            </div>
        </div>
    </div>
</div>

<?php
$refrashUrl = Yii::$app->urlManager->createUrl(['softins/create']);
$this->registerJs("
$('#myModal').on('hidden.bs.modal', function (e) {
    window.location.replace('{$refrashUrl}');
})
", $this::POS_READY);
if(!empty($showModalSwitcher))
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
/**/
?>

<div class="rsoftware-installed-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=
        $form->field($model, 'software')->dropDownList(
            ArrayHelper::map(
                RSoftware::find()
                    ->select(['r_software.id', 'concat(
                                                    cts.name, ", ",
                                                    r_software.name, " ",
                                                    r_software.version
                                                ) as name'])
                    ->join('INNER JOIN', 'c_type_software cts', 'cts.id = r_software.type')
                    ->orderBy('cts.name, r_software.name, r_software.version')
                    ->asArray()
                    ->All(), 'id', 'name'
            ),
            ['prompt'=>'Выберите элемент']
        )->label('Дистрибутив ПО')
    ?>

    <?=
        $form->field($model, 'equipment')->dropDownList(
        ArrayHelper::map(
            REquipment::find()->select('id, name')->orderBy('name')->asArray()->All(), 'id', 'name'),
            ['prompt'=>'Выберите элемент']
        )->label('Где установлено')
    ?>

    <?= $form->field($model, 'description')->textArea(['maxlength' => 2048]) ?>

    <?= $form->field($model, 'date_commission')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru-Ru',
        // 'dateFormat' => 'yyyy-MM-dd',
        'dateFormat' => 'dd.MM.yyyy',
        'options' => [
            'class' => 'form-control',
        ]
    ]) ?>

    <?= $form->field($model, 'bitrate')->dropDownList([ 32 => '32', 64 => '64', ], ['prompt'=>'Выберите элемент']) ?>

    <?php // Информационные системы ?>
    <?php echo $this->render('_form/_block_rinformationsystem', ['model' => $model, 'form' => $form]); ?>

    <?php // Взаимосвязь с другим установленным ПО ?>
    <?php echo $this->render('_form/_block_rsoftinstalled', ['model' => $model, 'form' => $form]); ?>

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