<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Вы действительно хотите удалить информационную систему?' ?></h4>
            </div>
            <div class="modal-body">
                Данное действие удалит всю информацию об информационной системе,<br>
                включая все связи информационной системы с другими сущностями:<br>
                с контрактами, нормативно-правовыми актами, ответственными лицами,<br>
                программным обеспечением, все пароли, документацию, права доступа<br>
                пользователей к информационной системе.
            </div>
            <div class="modal-footer">
                <div class="text-left">
                    <input type="checkbox" id='confirm-delete-box1'/> Удалить связанные дистрибутивы<br>
                    <input type="checkbox" id='confirm-delete-box2'/> Удалить связанное установленное ПО
                </div>
                <input type="text" placeholder="Введите: да" id='confirm-delete-input'/>
                <button type="button" class="btn btn-primary" id='confirm-delete-button' data-dismiss="modal">Подтверждаю</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
<?php
// далее скрипты на JS
$search = <<< JS
    var applydelete = false; // удаление запрещено
    var deletetobject = null;
    // показать всплывающее окно удаления
    $('.showdelpopup').click(function(event) {
        deletetobject = $(this);
        $('#confirm-delete-button').attr('disabled', true);
        if (!applydelete) {
            $('#myModal').modal({'backdrop':'static'});
            $('#confirm-delete-input').val('');
            $('#confirm-delete-box1').prop("checked", false);
            $('#confirm-delete-box2').prop("checked", false);
            event.stopPropagation();
            event.preventDefault();
        }
    });
    // нажатие подтверждения
    $('#confirm-delete-button').click(function(event) {
        if ('да' == $('#confirm-delete-input').val()) {
            applydelete = true;
            if ( $('#confirm-delete-box1').prop("checked") ) {
                deletetobject.attr('href', deletetobject.attr('href') + '&dsc=1');
            }
            if ( $('#confirm-delete-box2').prop("checked") ) {
                deletetobject.attr('href', deletetobject.attr('href') + '&dsic=1');
            }
            deletetobject.click();
        }
    });

    $('#confirm-delete-input').keyup(function(event) {
        var state = ('да' == $('#confirm-delete-input').val());
        $('#confirm-delete-button').attr('disabled', !state);
    });
JS;
$this->registerJs($search, \yii\web\View::POS_READY);