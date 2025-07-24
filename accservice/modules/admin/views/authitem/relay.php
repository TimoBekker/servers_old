<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\AuthItem;
use app\models\AuthItemChild;
use yii\widgets\ActiveForm;
use app\models\registry\REquipment;
use app\models\registry\RInformationSystem;
use app\models\registry\RSoftware;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = 'Управление разрешениями на объекты группе: ' . ' ' . $model->alias;
$this->params['breadcrumbs'][] = ['label' => 'Управление группами и правами', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->alias, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = 'Управление разрешениями на объекты';


$equipRelays = ArrayHelper::getColumn($model->nnAuthitemEquipments, 'equipment');
$infosysRelays = ArrayHelper::getColumn($model->nnAuthitemIs, 'information_system');
$softRelays = ArrayHelper::getColumn($model->nnAuthitemSoftwares, 'software');
// var_dump($equipRelays);
// var_dump($infosysRelays);
// var_dump($softRelays);

?>
<div class="auth-item-relay">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="auth-item-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-lg-4">

                <div class="form-group">
                    <?= Html::label('Оборудование') ?>
                    <div class="multiselect" name="testname">
                        <?php foreach (REquipment::find()->orderBy('name')->All() as $equipModel): ?>
                            <?php $checked = in_array($equipModel->id, $equipRelays); ?>
                            <div class="ms-item <?= $checked ? 'checked' : '' ?>">
                                <label>
                                    <table>
                                        <tr>
                                            <td>
                                                <input
                                                    name = "ItemEquipments[]" value = "<?= $equipModel->id ?>"
                                                    class="multiselect-input equip"
                                                    type="checkbox"
                                                    <?= $checked ? 'checked' : '' ?>
                                                />
                                            </td>
                                            <td><div><?= $equipModel->name ?></div></td>
                                        </tr>
                                    </table>
                                </label>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::button('Выбрать все', ['class' => 'btn btn-default', 'id' => 'select-all-items-equip']) ?>
                    <?= Html::button('Сбросить все', ['class' => 'btn btn-default', 'id' => 'clear-all-items-equip']) ?>
                </div>

            </div>
            <div class="col-lg-4">

                <div class="form-group">
                    <?= Html::label('Информационные системы') ?>
                    <div class="multiselect" name="testname">
                        <?php foreach (RInformationSystem::find()->orderBy('name_short')->All() as $infosysModel): ?>
                            <?php $checked = in_array($infosysModel->id, $infosysRelays); ?>
                            <div class="ms-item <?= $checked ? 'checked' : '' ?>">
                                <label>
                                    <table>
                                        <tr>
                                            <td>
                                                <input
                                                    name = "ItemInfosys[]" value = "<?= $infosysModel->id ?>"
                                                    class="multiselect-input infosys"
                                                    type="checkbox"
                                                    <?= $checked ? 'checked' : '' ?>
                                                />
                                            </td>
                                            <td><div><?= $infosysModel->name_short ?></div></td>
                                        </tr>
                                    </table>
                                </label>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::button('Выбрать все', ['class' => 'btn btn-default', 'id' => 'select-all-items-infosys']) ?>
                    <?= Html::button('Сбросить все', ['class' => 'btn btn-default', 'id' => 'clear-all-items-infosys']) ?>
                </div>

            </div>
            <div class="col-lg-4">

                <div class="form-group">
                    <?= Html::label('Программное обеспечение') ?>
                    <div class="multiselect" name="testname">
                        <?php foreach (RSoftware::find()->orderBy('name')->All() as $softModel): ?>
                            <?php $checked = in_array($softModel->id, $softRelays); ?>
                            <div class="ms-item <?= $checked ? 'checked' : '' ?>">
                                <label>
                                    <table>
                                        <tr>
                                            <td>
                                                <input
                                                    name = "ItemSoft[]" value = "<?= $softModel->id ?>"
                                                    class="multiselect-input soft"
                                                    type="checkbox"
                                                    <?= $checked ? 'checked' : '' ?>
                                                />
                                            </td>
                                            <td><div><?= $softModel->name ?></div></td>
                                        </tr>
                                    </table>
                                </label>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="form-group">
                    <?= Html::button('Выбрать все', ['class' => 'btn btn-default', 'id' => 'select-all-items-soft']) ?>
                    <?= Html::button('Сбросить все', ['class' => 'btn btn-default', 'id' => 'clear-all-items-soft']) ?>
                </div>

            </div>
        </div>


        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<?php
$search = <<< JS
    $('#select-all-items-equip').click(function(event) {
        var ms = $('.multiselect-input.equip').prop('checked', 'checked');
        ms.parents('.ms-item').addClass('checked');
    });
    $('#clear-all-items-equip').click(function(event) {
        var ms = $('.multiselect-input.equip').prop('checked', '');
        ms.parents('.ms-item').removeClass('checked');
    });
    $('#select-all-items-infosys').click(function(event) {
        var ms = $('.multiselect-input.infosys').prop('checked', 'checked');
        ms.parents('.ms-item').addClass('checked');
    });
    $('#clear-all-items-infosys').click(function(event) {
        var ms = $('.multiselect-input.infosys').prop('checked', '');
        ms.parents('.ms-item').removeClass('checked');
    });
    $('#select-all-items-soft').click(function(event) {
        var ms = $('.multiselect-input.soft').prop('checked', 'checked');
        ms.parents('.ms-item').addClass('checked');
    });
    $('#clear-all-items-soft').click(function(event) {
        var ms = $('.multiselect-input.soft').prop('checked', '');
        ms.parents('.ms-item').removeClass('checked');
    });
    $('.ms-item').click(function(event) {
        var input = $('.multiselect-input', this);
        if ( !input.prop('checked') ) {
            input.prop('checked', 'chicked');
            $(this).addClass('checked');
        } else {
            input.prop('checked', '');
            $(this).removeClass('checked');
        }
    });
    $('.multiselect-input').change(function(event) {
        var item = $(this).parent().parent().parent().parent().parent().parent();
        if(this.checked){
            item.addClass('checked');
        }
        else{
            item.removeClass('checked');
        }
    });
JS;
$this->registerJs($search, $this::POS_READY);
$this->registerCss("
    div.multiselect{
        border: 1px solid lightgray;
        max-height: 560px;
        min-height: 560px;
        overflow-y: scroll;
    }
    div.multiselect .ms-item{
        margin: 4px;
        border: 1px solid lightgray;
    }
    div.multiselect .ms-item table td{
        padding: 12px 0px 2px 10px;
        font-weight: normal;
    }
    div.multiselect .ms-item.checked{
        background-color: cornflowerblue;
        color: white;
    }
");