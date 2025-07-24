<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$tmpoffice = $model->placement ? $model->placement0->office : '';
$tmplocker = $model->placement ? $model->placement0->locker : '';
$tmpshelf = $model->placement ? $model->placement0->shelf : '';
?>

<?= $form->beginField($model, 'placement'); ?>
    <?= Html::activeLabel($model, 'placement'); ?>
    <div class="input-group">
        <?= Html::dropDownList('Partplace[alias]', $model->partplace, ArrayHelper::map(
            Yii::$app->db->createCommand('
                SELECT `alias`
                FROM `view_partplacement`
                ORDER BY `region`, `city`, `street`, `house`
            ')->queryAll(), 'alias', 'alias'), ['prompt'=>'Выберите элемент', 'class'=>'form-control', 'style'=>'z-index:0']) ?>
        <span class="input-group-btn" style="width:0px"></span>
        <?= Html::input(
            'text',
            "Partplace[office]",
            $tmpoffice,
            [
                'class' => 'form-control',
                'placeholder'=>'Кабинет',
                'style'=>'z-index:0',
                'readonly' => $tmpoffice || $model->partplace ? false : true,
            ]
        ) ?>
        <span class="input-group-btn" style="width:0px"></span>
        <?= Html::input(
            'text',
            "Partplace[locker]",
            $tmplocker,
            [
                'class' => 'form-control',
                'placeholder'=>'Шкаф',
                'style'=>'z-index:0',
                'readonly' => $tmplocker || $tmpoffice ? false : true,
            ]
        ) ?>
        <span class="input-group-btn" style="width:0px"></span>
        <?= Html::input(
            'text',
            "Partplace[shelf]",
            $tmpshelf,
            [
                'class' => 'form-control',
                'placeholder'=>'Unit(снизу)',
                'style'=>'z-index:0',
                'readonly' => $tmpshelf || $tmplocker ? false : true,
            ]
        ) ?>
        <span class="input-group-btn">
            <?= Html::a('Добавить новое место', ['refs/create', 'refid'=>'SPlacement'], ['class' => 'btn btn-success', 'target'=>'_blank']) ?>
        </span>
    </div>
<?= $form->endField() ?>
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