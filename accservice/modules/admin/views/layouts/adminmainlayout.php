<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="img/logo.png" type="image/png">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'ЛК',
                // 'brandUrl' => Yii::$app->homeUrl,
                'brandUrl' => ['default/index'],
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            $menuItems = [
                ['label' => '<span class="visible-lg">Мой профиль</span><span class="hidden-lg">Профиль</span>', 'url' => ['user/selfupdate']],
                [
                    'label' => 'Управление польз<span class="hidden-sm">ователями и группами</span>',
                    'url' => ['index'],
                    'options' => [
                        'class' => (Yii::$app->controller->id == 'authitem' /*|| Yii::$app->controller->id == 'user'*/) ? 'active' : '',
                    ],
                    'items' => [
                        ['label' => 'Управление пользователями', 'url' => ['user/index']],
                        ['label' => 'Управление группами и правами', 'url' => ['authitem/index']],
                        // ['label' => 'Редактирование своей учетной записи', 'url' => ['user/selfupdate']],
                        // ['label' => 'Управление правами групп', 'url' => '#'],
                        // ['label' => 'Управление соответствиями между оборудованием, ИС, ПО и группами', 'url' => '#'],
                    ],
                    'options' => [
                        'class' => (( Yii::$app->controller->id == 'user' && Yii::$app->controller->action->id != 'selfupdate') || Yii::$app->controller->id == 'authitem') ? 'active' : '',
                    ],
                ],
                ['label' => '<span class="visible-lg">Просмотр логов системы</span><span class="hidden-lg">Лог</span>', 'url' => ['log/index', 'sort' => '-date_emergence']],
                ['label' => 'Выход из ЛК', 'url' => ['/site/index']],
            ];
            echo '
                <form class="navbar-form navbar-left" role="search" action="">
                    <input type="hidden" name="r" value="site/search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Поиск" name="searchstring" style="width:180px">
                    </div>
                    <!-- <button type="submit" class="btn btn-default">Отправить</button> -->
                </form>
            ';
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
                'encodeLabels' => false,
            ]);
            NavBar::end();
        ?>

        <div class="container">

        <?= Breadcrumbs::widget([
            'homeLink' => false, // внутри лк первую ссылку в крошках не выводим
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
