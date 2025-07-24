<?php
use yii\helpers\Url;

$replaceurl = Url::toRoute(['index']);
$replaceurldelete = Url::toRoute(['delete', 'id' => $id, 'applyconfirm' => 1]);
?>
<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a href="<?=Url::toRoute(['index'])?>" class="close">&times;</a>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Внимание!' ?></h4>
            </div>
            <div class="modal-body">
                Удаление данного пользователя приведёт к безвозвратной <br/>
                потере всей информации, связанной с этим пользователем.<br/>
                Например, для оборудования и информационных систем не <br/>
                сохранится информация о том, какую пользователь нёс за<br/>
                них ответственность.<br/>
                <br/>
                Вы действительно хотите удалить данного пользователя?
            </div>
            <div class="modal-footer">
                <input type="text" placeholder="Введите: да" id='confirm-delete-input'/>
                <button type="button" class="btn btn-primary" id='confirm-delete-button' data-dismiss="modal" disabled>Подтверждаю</button>
                <a href="<?=Url::toRoute(['index'])?>" class="btn btn-default">Отмена</a>
            </div>
        </div>
    </div>
</div>
<?php
// далее скрипты на JS
$search = <<< JS
    // нажатие подтверждения
    $('#confirm-delete-button').click(function(event) {
        if ('да' == $('#confirm-delete-input').val()) {
            $.post('{$replaceurldelete}', {'' : ''}, function(data, textStatus, xhr) {
                /*просто передали постом, потому что метод делете ждет постом данные*/
            });
        } else {
            location.replace('{$replaceurl}');
        }
    });

    $('#confirm-delete-input').keyup(function(event) {
        var state = ('да' == $('#confirm-delete-input').val());
        $('#confirm-delete-button').attr('disabled', !state);
    });
JS;
$this->registerJs($search, \yii\web\View::POS_READY);