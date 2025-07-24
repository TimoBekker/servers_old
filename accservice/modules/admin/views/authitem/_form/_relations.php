<?php
/* группа справа, а все, которые в нее входят - слева */

use yii\helpers\Html;
use app\models\AuthItem;
use app\models\AuthItemChild;

$lefts = $leftsID = [];
$lefts = $model->authItemParents;
foreach ($lefts as $key => $val) {
    $leftsID[] = $val->parent0->name;
}
// var_dump($lefts);
// foreach ($lefts as $key => $value) {
//     var_dump($value->child0->name);
// }
?>

<div class="form-group">
    <?= Html::label('Объекты, входящие в группу') ?>
    <div class="multiselect" name="testname">
        <?php foreach ($model->find()->where('`type` in (2, 3) and `name` != :id', [':id' => $model->name ?: ''])->All() as $key => $value): ?>
            <?php $checked = in_array($value->name, $leftsID); ?>
            <div class="ms-item <?= $checked ? 'checked' : '' ?>">
                <label>
                    <table>
                        <tr>
                            <td>
                                <input
                                    name = "ItemParents[]" value = "<?= $value->name ?>"
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
// where $position can be View::POS_READY (the default),
// or View::POS_HEAD, View::POS_BEGIN, View::POS_END

// далее CSS
$this->registerCss("

    div.multiselect{
        border: 1px solid lightgray;
        max-height: 320px;
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