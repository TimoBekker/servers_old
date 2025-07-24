<?php
use yii\web\View;
use yii\helpers\Html;
use app\models\registry\RPassword;
use app\components\utils\Cryptonite;
?>

<?php // if ($arrPasswords = $model->rPasswords): ?>

<?php if ($model->rPasswords && $arrPasswords = RPassword::find()->where(['equipment' => $model->id, 'finale'=>0, 'next'=>null])->All()): ?>

    <?= Html::script('var rpasswordMax = '.(count($arrPasswords)-1), []); ?>
    <?php foreach ($arrPasswords as $keyv => $v): ?>

        <?php // var_dump($v->); ?>
        <?= $form->beginField(new RPassword, "[{$keyv}]rpasswords"); ?>
            <?php if ($keyv == 0): ?>
                <label class="control-label" for="rpassword-rpasswords">Данные аутентификации</label>
            <?php endif ?>
            <div class="input-group">
                <?= Html::input(
                    'text',
                    "RPassword[{$keyv}][login]",
                    $v->login,
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Логин',
                        'style'=>'z-index:0'
                    ]
                ) ?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::input(
                    'password',
                    "RPassword[{$keyv}][password]",
                    Cryptonite::decodePassword($v->password),
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Новый пароль',
                        'style'=>'z-index:0',
                        'data-num' => "{$keyv}"
                    ]
                ) ?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::input(
                    'password',
                    "RPassword[{$keyv}][confirmation]", // поле подтверждения пароля
                    Cryptonite::decodePassword($v->password),
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Подтверждение пароля',
                        'style'=>'z-index:0',
                        'data-num' => "{$keyv}"
                    ]
                ) ?>
                <span class="input-group-btn" style="width:0px"></span>
                <?= Html::input(
                    'text',
                    "RPassword[{$keyv}][description]",
                    $v->description,
                    [
                        'class' => 'form-control',
                        'placeholder'=>'Описание',
                        'style'=>'z-index:0'
                    ]
                ) ?>
                <?php // скрытое поле с айдишником?>
                <?= Html::Input(
                    'hidden',
                    "RPassword[{$keyv}][currentid]",
                    $v->id, []
                ) ?>
                <span class="input-group-btn"></span>
                <span class="input-group-btn">
                    <button class="btn btn-success field-create-rpassword" type="button">+</button>
                    <button class="btn btn-danger field-remove <?=$keyv ? '' : 'disabled' ?>" type="button">x</button>
                </span>
            </div>
            <div class="help-block"></div>
        <?= $form->endField(); ?>

    <?php endforeach ?>
<?php else: ?>
    <?= Html::script('var rpasswordMax = 0', []); ?>
    <?= $form->beginField(new RPassword, '[0]rpasswords'); ?>
        <label class="control-label" for="rpassword-rpasswords">Данные аутентификации</label>
        <div class="input-group">
            <?= Html::input(
                'text',
                'RPassword[0][login]',
                '',
                [
                	'class' => 'form-control',
                	'placeholder'=>'Логин',
                	'style'=>'z-index:0',
                ]
            ) ?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'password',
                'RPassword[0][password]',
                '',
                [
                    'class' => 'form-control',
                    'placeholder'=>'Пароль',
                    'style'=>'z-index:0',
                    'data-num' => '0'
                ]
            ) ?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'password',
                'RPassword[0][confirmation]', // поле подтверждения пароля
                '',
                [
                    'class' => 'form-control',
                    'placeholder'=>'Подтверждение пароля',
                    'style'=>'z-index:0',
                    'data-num' => '0'
                ]
            ) ?>
            <span class="input-group-btn" style="width:0px"></span>
            <?= Html::input(
                'text',
                'RPassword[0][description]',
                '',
                [
                    'class' => 'form-control',
                    'placeholder'=>'Описание',
                    'style'=>'z-index:0'
                ]
            ) ?>
            <span class="input-group-btn"></span>
            <span class="input-group-btn">
                <button class="btn btn-success field-create-rpassword" type="button">+</button>
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
    jQuery(document).on('click', '.field-create-rpassword',function(event) {
        var newel;
        $(this).parent().parent().parent().after(newel = $(this).parent().parent().parent().clone());
        newel.children('label').remove();
        rpasswordMax++;
        newel.attr('class', 'form-group field-rpassword-'+(rpasswordMax)+'customgroup' );

        newel.find('input[name *= "login"]').attr('name', 'RPassword['+(rpasswordMax)+'][login]');
        newel.find('input[name *= "login"]').val('');
        newel.find('input[name *= "password"]').attr('name', 'RPassword['+(rpasswordMax)+'][password]');
        newel.find('input[name *= "password"]').attr('data-num', rpasswordMax);
        newel.find('input[name *= "password"]').attr('placeholder', 'Пароль');
        newel.find('input[name *= "password"]').val('');
        newel.find('input[name *= "password"]').css('outline', '');
        newel.find('input[name *= "confirmation"]').attr('name', 'RPassword['+(rpasswordMax)+'][confirmation]');
        newel.find('input[name *= "confirmation"]').attr('data-num', rpasswordMax);
        newel.find('input[name *= "confirmation"]').val('');
        newel.find('input[name *= "confirmation"]').css('outline', '');
        newel.find('input[name *= "description"]').attr('name', 'RPassword['+(rpasswordMax)+'][description]');
        newel.find('input[name *= "description"]').val('');
        newel.find('input[name *= "currentid"]').remove();

        newel.find('button').removeClass('disabled');
    });

    jQuery(document).on('submit', '#w0', function(event) {
        $('input[name *= "password"]').each(function(index, el) {
            var notNorm = false;
            var num = $(this).attr('data-num');
            var pass = $(this).val();
            var confirmInput = $('input[name *= "confirmation"][data-num = "' + num + '"]');
            var confirm = confirmInput.val();
            // console.log(num, pass, confirm);
            if ( pass !==  confirm) {
                notNorm = true;
                $(this).css('outline', '1px solid red');
                confirmInput.css('outline', '1px solid red');
            } else {
                $(this).css('outline', '');
                confirmInput.css('outline', '');
            }
            if (notNorm)
                event.preventDefault();
        });
    });
JS;
$this->registerJs($search, View::POS_READY);