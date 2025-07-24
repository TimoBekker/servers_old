<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$tmpoffice = $model->placement ? $model->placement0->office : '';
$tmplocker = $model->placement ? $model->placement0->locker : '';
$tmpshelf = $model->placement ? $model->placement0->shelf : '';
?>

    <div class="row">

        <div class="col-md-6">
            <?= Html::label('МЕСТО РАЗМЕЩЕНИЯ'); ?>
            <?= Html::dropDownList('Partplace[alias]', $model->partplace, ArrayHelper::map(
                Yii::$app->db->createCommand('
                    SELECT `alias`
                    FROM `view_partplacement`
                    ORDER BY `region`, `city`, `street`, `house`
                ')->queryAll(), 'alias', 'alias'), ['prompt'=>'Выберите элемент', 'class'=>'form-control input-sm', 'style'=>'z-index:0']) ?>
            <p class="help-block help-block-error"></p>
        </div>
        <div class="col-md-2">
            <?= Html::label('&nbsp;'); ?>
            <?= Html::input(
                'text',
                "Partplace[office]",
                $tmpoffice,
                [
                    'class' => 'form-control input-sm',
                    'placeholder'=>'Кабинет',
                    'style'=>'z-index:0',
                    'readonly' => $tmpoffice || $model->partplace ? false : true,
                ]
            ) ?>
        </div>
        <div class="col-md-2">
            <?= Html::label('&nbsp;'); ?>
            <?= Html::input(
                'text',
                "Partplace[locker]",
                $tmplocker,
                [
                    'class' => 'form-control input-sm',
                    'placeholder'=>'Шкаф',
                    'style'=>'z-index:0',
                    'readonly' => $tmplocker || $tmpoffice ? false : true,
                ]
            ) ?>
        </div>
        <div class="col-md-2">
            <?= Html::label('&nbsp;'); ?>
            <?= Html::input(
                'text',
                "Partplace[shelf]",
                $tmpshelf,
                [
                    'class' => 'form-control input-sm',
                    'placeholder'=>'Unit(сверху)',
                    'style'=>'z-index:0',
                    'readonly' => $tmpshelf || $tmplocker ? false : true,
                ]
            ) ?>
        </div>

    </div>

<?php
$search = <<< JS
    $('select[name="Partplace[alias]"]').change(function(event) {
        if ( $('option:selected', this).val() !== '' ) {
            // alert();
            $('input[name="Partplace[office]"]').attr('readonly', false);
        } else {
            $('input[name="Partplace[office]"]').attr('readonly', true);
            $('input[name="Partplace[locker]"]').attr('readonly', true);
            $('input[name="Partplace[shelf]"]').attr('readonly', true);
            $('input[name="Partplace[office]"]').val('');
            $('input[name="Partplace[locker]"]').val('');
            $('input[name="Partplace[shelf]"]').val('');
        }
    });
    $('input[name="Partplace[office]"]').keyup(function(event) {
        if ( $(this).val() !== '' ) {
            $('input[name="Partplace[locker]"]').attr('readonly', false);
        } else {
            $('input[name="Partplace[locker]"]').attr('readonly', true);
            $('input[name="Partplace[shelf]"]').attr('readonly', true);
            $('input[name="Partplace[locker]"]').val('');
            $('input[name="Partplace[shelf]"]').val('');
        }
    });
    $('input[name="Partplace[locker]"]').keyup(function(event) {
        if ( $(this).val() !== '' ) {
            $('input[name="Partplace[shelf]"]').attr('readonly', false);
        } else {
            $('input[name="Partplace[shelf]"]').attr('readonly', true);
            $('input[name="Partplace[shelf]"]').val('');
        }
    });

JS;
$this->registerJs($search, $this::POS_READY);