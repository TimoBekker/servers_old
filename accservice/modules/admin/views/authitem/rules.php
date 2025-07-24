<?php
/* справа правила - слева зависимые группы */

use yii\helpers\Html;
use app\models\AuthItem;
use app\models\AuthItemChild;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = 'Управление правами группы: ' . ' ' . $model->alias;
$this->params['breadcrumbs'][] = ['label' => 'Управление группами и правами', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->alias, 'url' => ['view', 'id' => $model->name]];
$this->params['breadcrumbs'][] = 'Управление правами';


$rights = $rightsID = [];
$rights = $model->authItemChildren;
foreach ($rights as $key => $val) {
    $rightsID[] = $val->child0->name;
}
// var_dump($rightsID);
?>
<div class="auth-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="auth-item-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="form-group">
            <?= Html::label('Доступные права') ?>
            <div class="multiselect" name="testname">
                <?php foreach ($model->find()->where('`type`= 1')->All() as $key => $value): ?>
                    <?php $checked = in_array($value->name, $rightsID); ?>
                    <div class="ms-item <?= $checked ? 'checked' : '' ?>">
                        <label>
                            <table>
                                <tr>
                                    <td>
                                        <input
                                            name = "ItemRights[]" value = "<?= $value->name ?>"
                                            class="multiselect-input"
                                            type="checkbox"
                                            <?= $checked ? 'checked' : '' ?>
                                        />
                                    </td>
                                    <td><div><?= $value->alias ?></div></td>
                                </tr>
                            </table>
                        </label>
                    </div>
                <?php endforeach ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::button('Выбрать всех', ['class' => 'btn btn-default', 'id' => 'select-all-items']) ?>
            <?= Html::button('Сбросить всех', ['class' => 'btn btn-default', 'id' => 'clear-all-items']) ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
<?php
// JS скрипты
$search = <<< JS
    $('#select-all-items').click(function(event) {
        var ms = $('.multiselect-input').prop('checked', 'checked');
        ms.parents('.ms-item').addClass('checked');
    });
    $('#clear-all-items').click(function(event) {
        var ms = $('.multiselect-input').prop('checked', '');
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
        // max-height: 320px;
        // overflow-y: scroll;
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