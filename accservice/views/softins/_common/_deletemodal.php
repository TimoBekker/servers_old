<?php // Модальное диалоговое окно ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?= $modalName = 'Вы действительно хотите удалить установленное ПО?' ?></h4>
            </div>
            <div class="modal-body">
                Данное действие удалит всю информацию о данном установленном экземпляре ПО.
            </div>
            <div class="modal-footer">
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
        if (!applydelete) {
            $('#myModal').modal({'backdrop':'static'});
            event.stopPropagation();
            event.preventDefault();
        }
    });
    // нажатие подтверждения
    $('#confirm-delete-button').click(function(event) {
        applydelete = true;
        deletetobject.click();
    });
JS;
$this->registerJs($search, \yii\web\View::POS_READY);