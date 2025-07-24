<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\registry\RSoftwareInstalled;
use app\models\relation\NnSoftinstallSoftinstall;
?>

<?php if ($arrNnSoftinstallSoftinstall = $model->nnSoftinstallSoftinstalls): ?>
    <?= Html::script('var softwareinstalledMax = '.(count($arrNnSoftinstallSoftinstall)-1), []); ?>
    <?php foreach ($arrNnSoftinstallSoftinstall as $keyv => $v): ?>

        <?php // var_dump($v->); ?>
        <?= $form->beginField(new NnSoftinstallSoftinstall, "[{$keyv}]nnsoftinstallsoftinstalls"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nnsoftinstallsoftinstall-nnsoftinstallsoftinstalls">Связь с другим установленным ПО</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::DropDownList(
                    "NnSoftinstallSoftinstall[{$keyv}][software_installed2]",
                    $v->softwareInstalled2->id,
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
                    <button class="btn btn-success field-create-software_installed2" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var softwareinstalledMax = 0', []); ?>
    <?= $form->beginField(new NnSoftinstallSoftinstall, '[0]nnsoftinstallsoftinstalls'); ?>
        <label class="control-label" for="nnsoftinstallsoftinstall-nnsoftinstallsoftinstalls">Связь с другим установленным ПО</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnSoftinstallSoftinstall[0][software_installed2]',
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
                <button class="btn btn-success field-create-software_installed2" type="button">+</button>
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
    easyInputManage('software_installed2','softwareinstalledMax','NnSoftinstallSoftinstall');
JS;
$this->registerJs($search, \yii\web\View::POS_READY);