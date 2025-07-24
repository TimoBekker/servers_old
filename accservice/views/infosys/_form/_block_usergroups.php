<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\relation\NnAuthitemIs;
use app\models\AuthItem;
?>

<?php if ($arrAuthitemIs = $model->nnAuthitemIs): ?>
    <?= Html::script('var usergroupMax = '.(count($arrAuthitemIs)-1), []); ?>
    <?php foreach ($arrAuthitemIs as $keyv => $v): ?>
        <?php // var_dump($v->auth_item); ?>
        <?= $form->beginField(new NnAuthitemIs, "[{$keyv}]nnauthitemis"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="nnauthitemis-nnauthitemis">Редактирование информационной системы разрешено</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::DropDownList(
                    "NnAuthitemIs[{$keyv}][auth_item]",
                    $v->authItem->name, // здесь нет айдишника, а первичный ключ-нейм
                    ArrayHelper::map(
                        AuthItem::find()
                            ->select('name, alias')
                            ->where(['type' => [2,3]]) // жестко, кде тип равен 2 или 3
                            ->orderBy('alias')
                            ->asArray()
                            ->All(), 'name', 'alias'
                    ),
                    [
                        'prompt'=>'Выберите элемент',
                        'class'=>'form-control',
                        'style'=>'z-index:0'
                    ]
                )?>
                <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-auth_item" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var usergroupMax = 0', []); ?>
    <?= $form->beginField(new NnAuthitemIs, '[0]nnauthitemis'); ?>
        <label class="control-label" for="nnauthitemis-nnauthitemis">Редактирование информационной системы разрешено</label>
        <div class="input-group">
            <?= Html::DropDownList(
                'NnAuthitemIs[0][auth_item]',
                $isCreateScena ? 'auto_item_new_user_'.(string)Yii::$app->user->id : false,
                ArrayHelper::map(
                    AuthItem::find()
                        ->select('name, alias')
                        ->where(['type' => [2,3]]) // жестко, кде тип равен 2 или 3
                        ->orderBy('alias')
                        ->asArray()
                        ->All(), 'name', 'alias'
                ),
                [
                    'prompt'=>'Выберите элемент',
                    'class'=>'form-control',
                    'style'=>'z-index:0'
                ]
            )?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-auth_item" type="button">+</button>
                <button class="btn btn-danger field-remove disabled" type="button">x</button>
            </span>
        </div>
        <div class="help-block"></div>
    <?= $form->endField(); ?>
<?php endif ?>
<?php
// далее скрипты на JS
$search = <<< JS
    //заявки
    easyInputManage('auth_item','usergroupMax','NnAuthitemIs');
JS;
$this->registerJs($search, View::POS_READY);