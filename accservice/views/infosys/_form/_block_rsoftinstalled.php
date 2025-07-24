<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnIsSoftinstall;
use app\models\registry\RSoftwareInstalled;
?>

<?php if ($arrNnIsSoftinstall = $model->nnIsSoftinstalls): ?>
    <?= Html::script('var softwareMax = '.(count($arrNnIsSoftinstall)-1), []); ?>
    <?php foreach ($arrNnIsSoftinstall as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnIsSoftinstall, "[{$keyv}]nnissoftinstalls"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nnissoftinstall-nnissoftinstalls">Связь с установленным ПО</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::DropDownList(
                    "NnIsSoftinstall[{$keyv}][software_installed]",
                    $v->softwareInstalled->id,
                    ArrayHelper::map(
                        RSoftwareInstalled::find()
                            ->select(['r_software_installed.id', 'concat(
                                                            c_type_software.name, ", ",
                                                            r_software.name, " ",
                                                            r_software.version, " ( ",
                                                            r_equipment.name, " )"
                                                        ) as name'])
                            ->join('INNER JOIN', 'r_software', 'r_software.id = r_software_installed.software')
                            ->join('INNER JOIN', 'c_type_software', 'c_type_software.id = r_software.type')
                            ->join('INNER JOIN', 'r_equipment', 'r_equipment.id = r_software_installed.equipment')
                            ->orderBy('c_type_software.name, r_software.name, r_software.version, r_equipment.name')
                            ->asArray()
                            ->All(), 'id', 'name'
                    ),
                    [
                        'prompt'=>'Выберите элемент',
                        'class'=>'form-control',
                        'style'=>'z-index:0'
                    ]
                )?>
                <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-software_installed" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var softwareMax = 0', []); ?>
    <?= $form->beginField(new NnIsSoftinstall, '[0]nnissoftinstalls'); ?>
        <label class="control-label" for="nnissoftinstall-nnissoftinstalls">Связь с установленным ПО</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnIsSoftinstall[0][software_installed]',
                false,
                ArrayHelper::map(
                    RSoftwareInstalled::find()
                        ->select(['r_software_installed.id', 'concat(
                                                        c_type_software.name, ", ",
                                                        r_software.name, " ",
                                                        r_software.version, " ( ",
                                                        r_equipment.name, " )"
                                                    ) as name'])
                        ->join('INNER JOIN', 'r_software', 'r_software.id = r_software_installed.software')
                        ->join('INNER JOIN', 'c_type_software', 'c_type_software.id = r_software.type')
                        ->join('INNER JOIN', 'r_equipment', 'r_equipment.id = r_software_installed.equipment')
                        ->orderBy('c_type_software.name, r_software.name, r_software.version, r_equipment.name')
                        ->asArray()
                        ->All(), 'id', 'name'
                ),
                [
                    'prompt'=>'Выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-software_installed" type="button">+</button>
                <button class="btn btn-danger field-remove disabled" type="button">x</button>
            </span>
        </div>
        <div class="help-block"></div>
    <?= $form->endField(); ?>
<?php endif ?>
<?php
// далее скрипты на JS
$search = <<< JS
    // контракты
    easyInputManage('software_installed','softwareMax','NnIsSoftinstall');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);