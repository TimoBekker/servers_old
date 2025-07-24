<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\reference\CTypeLegalDoc;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\registry\RLegalDoc */
/* @var $form yii\widgets\ActiveForm */
?>

<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Нормативно-правовой акт успешно добавлен' ?></h4>
            </div>
            <!-- <div class="modal-body">
            </div> -->
            <div class="modal-footer">
                <?= Html::a('Перейти', ['ldoc/view', 'id' => isset($switcherId) ? $switcherId : ''], ['class' => 'btn btn-primary'])?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Добавить новый</button>
            </div>
        </div>
    </div>
</div>

<?php
$refrashUrl = Yii::$app->urlManager->createUrl(['ldoc/create']);
$this->registerJs("
$('#myModal').on('hidden.bs.modal', function (e) {
    window.location.replace('{$refrashUrl}');
})
", $this::POS_READY);
if(!empty($showModalSwitcher))
    $this->registerJs("$('#myModal').modal({'backdrop':'static'});", $this::POS_READY);
/**/
?>

<div class="rlegal-doc-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?=
    $form->field($model, 'type')
        ->dropDownList(
            ArrayHelper::map(CTypeLegalDoc::find()->select('id, name')->asArray()->All(), 'id', 'name'),
            ['prompt'=>'Выберите элемент']
        )
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 512]) ?>

    <?= $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'ru-Ru',
        // 'dateFormat' => 'yyyy-MM-dd',
        'dateFormat' => 'dd.MM.yyyy',
        'options' => [
            'class' => 'form-control',
        ]
    ]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'regulation_subject')->textInput(['maxlength' => 512]) ?>

    <?php //= $form->field($model, 'status')->dropDownList([ 0 => 'Действует', 1 => 'Отменен', ]) ?>

    <?php // инвертируем статус ?>
    <?php $model->status = $model->status == '1' ? '0' : '1'; // здесь обязательно нестрогое сравнение?>
    <?= $form->field($model, 'status')->checkbox(/*['label' => 'I agree', 'checked'=>true]*/) ?>

    <?php // Контракты ?>
    <?php echo $this->render('_form/_block_rcontract', ['model' => $model, 'form' => $form]); ?>

    <?php // Информационные системы ?>
    <?php echo $this->render('_form/_block_rinformationsystem', ['model' => $model, 'form' => $form]); ?>

    <?php // = $form->field($model, 'url')->textInput(['maxlength' => 2048]) ?>
    <?php // = $form->field($model, 'url')->fileInput(['class'=>'form-control'])->label('Ссылка на файл с нормативно правовым актом') ?>
    <?= $form->beginField($model, 'url'); ?>
        <?= Html::label('Ссылка на файл с нормативно правовым актом') ?>
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
    // where $position can be View::POS_READY (the default),
    // or View::POS_HEAD, View::POS_BEGIN, View::POS_END
