<?php
use yii\web\View;
use yii\helpers\Html;
use app\models\registry\RSoftwareInstalled;
?>

<?php if ($arrSoftwareInstalleds = $model->rSoftwareInstalleds): ?>
    <?= Html::script('var rsoftwareinstalledMax = '.(count($arrSoftwareInstalleds)-1), []); ?>
    <?php foreach ($arrSoftwareInstalleds as $keyv => $v): ?>

        <?php // var_dump($v->); ?>
        <?= $form->beginField(new RSoftwareInstalled, "[{$keyv}]sopenports"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="sopenport-sopenports">Установленное ПО</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::DropDownList(
                    "RSoftwareInstalled[{$keyv}][software]",
                    $v->software0->id,
                    $model->indexedSoftware,
                    [
                        'prompt'=>'Дистрибутив ПО: выберите элемент',
                        'class'=>'form-control',
                        'style'=>'z-index:0',
                    ]
                )?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::DropDownList(
                    "RSoftwareInstalled[{$keyv}][bitrate]",
                    $v->bitrate,
                    ['32'=>'32', '64'=>'64'],
                    [
                        'prompt'=>'Разрядность ПО: выберите элемент',
                        'class'=>'form-control',
                        'style'=>'z-index:0'
                    ]
                )?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= \yii\jui\DatePicker::widget([
                    'name' => "RSoftwareInstalled[{$keyv}][date_commission]",
                    'attribute' => 'from_date',
                    'language' => 'ru-Ru',
                    // 'dateFormat' => 'yyyy-MM-dd',
                    'dateFormat' => 'dd.MM.yyyy',
                    'value' => $v->date_commission,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder'=>'Дата ввода в эксплуатацию',
                    ]
                ]); ?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::input(
                    'text',
                    "RSoftwareInstalled[{$keyv}][description]",
                    $v->description,
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Описание',
                        'style'=>'z-index:0'
                    ]
                ) ?>
                <?php // скрытое поле с айдишником из таблицы документации ?>
                <?= Html::Input(
                    'hidden',
                    "RSoftwareInstalled[{$keyv}][updatedsoftinstid]",
                    $v->id,[]
                ) ?>
                <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-rsoftwareinstalled" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>


            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var rsoftwareinstalledMax = 0', []); ?>
    <?= $form->beginField(new RSoftwareInstalled, '[0]rsoftwareinstalleds'); ?>
        <label class="control-label" for="rsoftwareinstalled-rsoftwareinstalleds">Установленное ПО</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'RSoftwareInstalled[0][software]',
                false,
                $model->indexedSoftware,
                [
                    'prompt'=>'Дистрибутив ПО: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0',
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::DropDownList(
                'RSoftwareInstalled[0][bitrate]',
                false,
                ['32'=>'32', '64'=>'64'],
                [
                    'prompt'=>'Разрядность ПО: выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= \yii\jui\DatePicker::widget([
                'name' => 'RSoftwareInstalled[0][date_commission]',
                'attribute' => 'from_date',
                'language' => 'ru-Ru',
                // 'dateFormat' => 'yyyy-MM-dd',
                'dateFormat' => 'dd.MM.yyyy',
                'options' => [
                    'class' => 'form-control',
                    'placeholder'=>'Дата ввода в эксплуатацию',
                ]
            ]); ?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'text',
                "RSoftwareInstalled[0][description]",
                '',
                [
                    'class' => 'form-control',
                    'placeholder'=>'Описание',
                    'style'=>'z-index:0'
                ]
            ) ?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-rsoftwareinstalled" type="button">+</button>
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
    jQuery(document).on('click', '.field-create-rsoftwareinstalled',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        rsoftwareinstalledMax++;
        newel.attr('class', 'form-group field-rsoftwareinstalled-'+(rsoftwareinstalledMax)+'customgroup' );

        newel.find('select[name *= "software"]').attr({
            'name': 'RSoftwareInstalled['+(rsoftwareinstalledMax)+'][software]',
            'id': 'sopenport-'+(rsoftwareinstalledMax)+'-software'
        });
        newel.find('select[name *= "bitrate"]').attr({
            'name': 'RSoftwareInstalled['+(rsoftwareinstalledMax)+'][bitrate]',
            'id': 'sopenport-'+(rsoftwareinstalledMax)+'-bitrate'
        });
        newel.find('input[name *= "date_commission"]').attr('name', 'RSoftwareInstalled['+(rsoftwareinstalledMax)+'][date_commission]');
        newel.find('input[name *= "date_commission"]').val('');
        newel.find('input[name *= "date_commission"]').attr('id', '');
        newel.find('input[name *= "date_commission"]').removeClass('hasDatepicker');

        newel.find('input[name *= "description"]').attr('name', 'RSoftwareInstalled['+(rsoftwareinstalledMax)+'][description]');
        newel.find('input[name *= "description"]').val('');

        newel.find('button').removeClass('disabled');
    });

    $('body').on('mouseover', 'input[name *= "date_commission"]', function(e) {
        $(this).datepicker({dateFormat: "dd.mm.yy"});
    });
JS;
$this->registerJs($search, View::POS_READY);