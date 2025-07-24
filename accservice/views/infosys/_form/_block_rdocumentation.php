<?php
use yii\web\View;
use yii\helpers\Html;
use app\models\registry\RDocumentation;
?>

<?php if ($arrDocumentations = $model->rDocumentations): ?>
    <?= Html::script('var rdocumentationMax = '.(count($arrDocumentations)-1), []); ?>
    <?php foreach ($arrDocumentations as $keyv => $v): ?>

        <?php // var_dump($v->); ?>
        <?= $form->beginField(new RDocumentation, "[{$keyv}]rdocumentations"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="rdocumentation-rdocumentations">Документация</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::input(
                    'text',
                    "RDocumentation[{$keyv}][name]",
                    $v->name,
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Наименование',
                        'style'=>'z-index:0'
                    ]
                ) ?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::input(
                    'text',
                    "RDocumentation[{$keyv}][description]",
                    $v->description,
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Описание',
                        'style'=>'z-index:0'
                    ]
                ) ?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::Input(
                    'file',
                    "RDocumentation[{$keyv}][url]",
                    '',
                    [
                        'class' => 'form-control',
                        'style'=>'z-index:0'
                    ]
                ) ?>
                <span class="input-group-btn after-add-need-delete" style="width:0px"></span>
                <?php $currentDoc = basename($v->url) ?>
                <?= Html::a("Текущий: {$currentDoc}", "@web/{$v->url}", ['class' => 'form-control after-add-need-delete']) ?>
                <?php // скрытое поле с айдишником из таблицы документации ?>
                <?= Html::Input(
                    'hidden',
                    "RDocumentation[{$keyv}][currentdocid]",
                    $v->id,[]
                ) ?>
                <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-rdocumentation" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var rdocumentationMax = 0', []); ?>
    <?= $form->beginField(new RDocumentation, '[0]rdocumentations'); ?>
        <label class="control-label" for="rdocumentation-rdocumentations">Документация</label>
        <div class="input-group">
            <?= Html::input(
                'text',
                'RDocumentation[0][name]',
                '',
                [
                	'class' => 'form-control',
                	'placeholder'=>'Наименование',
                	'style'=>'z-index:0'
                ]
            ) ?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'text',
                'RDocumentation[0][description]',
                '',
                [
                    'class' => 'form-control',
                    'placeholder'=>'Описание',
                    'style'=>'z-index:0'
                ]
            ) ?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'file',
                'RDocumentation[0][url]',
                '',
                [
                    'class' => 'form-control',
                    'style'=>'z-index:0'
                ]
            ) ?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-rdocumentation" type="button">+</button>
                <button class="btn btn-danger field-remove disabled" type="button">x</button>
            </span>
        </div>
        <div class="help-block"></div>
    <?= $form->endField(); ?>
<?php endif ?>
<?php
// далее скрипты на JS
$search = <<< JS
    // добавление блоков
    jQuery(document).on('click', '.field-create-rdocumentation',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        rdocumentationMax++;
        newel.attr('class', 'form-group field-rdocumentation-'+(rdocumentationMax)+'customgroup' );

        newel.find('input[name *= "name"]').attr('name', 'RDocumentation['+(rdocumentationMax)+'][name]');
        newel.find('input[name *= "name"]').val('');
        newel.find('input[name *= "description"]').attr('name', 'RDocumentation['+(rdocumentationMax)+'][description]');
        newel.find('input[name *= "description"]').val('');
        newel.find('input[name *= "url"]').attr('name', 'RDocumentation['+(rdocumentationMax)+'][url]');
        newel.find('input[name *= "url"]').val('');

        newel.find('.after-add-need-delete').remove();
        newel.find('button').removeClass('disabled');
    });
JS;
$this->registerJs($search, View::POS_READY);