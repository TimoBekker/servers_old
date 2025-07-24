<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\registry\RSoftware;
use app\models\relation\NnSoftwareSoftware;
?>

<?php if ($arrNnSoftwareSoftware = $model->nnSoftwareSoftwares): ?>
    <?= Html::script('var softwareMax = '.(count($arrNnSoftwareSoftware)-1), []); ?>
    <?php foreach ($arrNnSoftwareSoftware as $keyv => $v): ?>

        <?php // var_dump($v->); ?>
        <?= $form->beginField(new NnSoftwareSoftware, "[{$keyv}]nnsoftwaresoftwares"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nnsoftwaresoftware-nnsoftwaresoftwares">Дистрибутивы, требуемые данному</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::DropDownList(
                    "NnSoftwareSoftware[{$keyv}][software2]",
                    $v->software20->id,
                    ArrayHelper::map(
                        RSoftware::find()
                            ->select(['r_software.id', 'concat(
                                                            cts.name, ", ",
                                                            r_software.name, " ",
                                                            r_software.version
                                                        ) as name'])
                            ->join('INNER JOIN', 'c_type_software cts', 'cts.id = r_software.type')
                            ->orderBy('cts.name, r_software.name, r_software.version')
                            ->asArray()
                            ->All(), 'id', 'name'
                    ),
                    [
                        'prompt'=>'Дистрибутив: выберите элемент',
                        'class'=>'form-control',
                        'style'=>'z-index:0'
                    ]
                )?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::input(
                    'text',
                    "NnSoftwareSoftware[{$keyv}][require_number]",
                    $v->require_number,
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Порядковый номер системного требования',
                        'maxlength'=>'255',
                    ]
                ) ?>
                <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-nnsoftwaresoftware" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var softwareMax = 0', []); ?>
    <?= $form->beginField(new NnSoftwareSoftware, '[0]nnsoftwaresoftwares'); ?>
        <label class="control-label" for="nnsoftwaresoftware-nnsoftwaresoftwares">Дистрибутивы, требуемые данному</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnSoftwareSoftware[0][software2]',
                false,
                ArrayHelper::map(
                    RSoftware::find()
                        ->select(['r_software.id', 'concat(
                                                        cts.name, ", ",
                                                        r_software.name, " ",
                                                        r_software.version
                                                    ) as name'])
                        ->join('INNER JOIN', 'c_type_software cts', 'cts.id = r_software.type')
                        ->orderBy('cts.name, r_software.name, r_software.version')
                        ->asArray()
                        ->All(), 'id', 'name'
                ),
                [
                    'prompt'=>'Дистрибутив: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'text',
                'NnSoftwareSoftware[0][require_number]',
                '',
                [
                	'class' => 'form-control',
                	'placeholder'=>'Порядковый номер системного требования',
                	'maxlength'=>'255',
                ]
            ) ?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-nnsoftwaresoftware" type="button">+</button>
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
    jQuery(document).on('click', '.field-create-nnsoftwaresoftware',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        softwareMax++;
        newel.attr('class', 'form-group field-nnsoftwaresoftware-'+(softwareMax)+'customgroup' );
        newel.find('select[name *= "software2"]').attr({
            'name': 'NnSoftwareSoftware['+(softwareMax)+'][software2]',
            'id': 'nnsoftwaresoftware-'+(softwareMax)+'-software2'
        });
        newel.find('input[name *= "require_number"]').attr('name', 'NnSoftwareSoftware['+(softwareMax)+'][require_number]');
        newel.find('input[name *= "require_number"]').val('');
        newel.find('button').removeClass('disabled');
    });
JS;
$this->registerJs($search, \yii\web\View::POS_READY);