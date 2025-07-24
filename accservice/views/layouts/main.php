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
				'brandLabel' => '<img src="img/logo.png" alt="РС">',
				'brandUrl' => Yii::$app->homeUrl,
				'options' => [
					'class' => 'navbar-inverse navbar-fixed-top',
                    'style' => 'box-shadow:1px 5px 10px rgba(102, 102, 102, 0.5)',
				],
				'brandOptions'=>[
					'style'=> 'padding:0 8px 0 15px',
				]
			]);
			// var_dump($_SERVER);exit;
			$menuItems = [
				/*[
					'label' => '<span class="visible-lg">Главная</span><span class="hidden-lg">Гл</span>',
					'url' => ['/site/index'],
					'options' => ['class' =>'hidden-sm'],
				],*/
				[
					'label' => '<span class="visible-lg">Оборудование</span><span class="hidden-lg">Об</span>',
					// 'url' => '', //будет подсветка только при /equip/index
					'url' => ['/equip/index'], //будет подсветка только при /equip/index
					'options' => [
						'class' => (Yii::$app->controller->id == 'equip') ? 'active' : '',
					],
					/*'items' => [
						['label' => 'Отображение / Поиск', 'url' => ['/equip/index']],
						['label' => 'Добавление', 'url' => ['/equip/create']],
					]*/
				],
				[
					'label' => 'ИС',
					'url' => ['/infosys/index'],
					'options' => [
						'title' => 'Информационные системы',
						'class' => (Yii::$app->controller->id == 'infosys') ? 'active' : '',
					],
					/*'items' => [
						['label' => 'Отображение / Поиск', 'url' => ['/infosys/index']],
						['label' => 'Добавление', 'url' => ['/infosys/create']],
					]*/
				],
				[
					'label' => 'ПО',
					'url' => ['/soft/index'],
					'options' => [
						'title' => 'Программное обеспечение',
						'class' => (Yii::$app->controller->id == 'soft' || Yii::$app->controller->id == 'softins') ? 'active' : '',
					],
					'items' => [
						['label' => 'Дистрибутивы ПО', 'url' => ['/soft/index']],
						['label' => 'Учет установленного ПО', 'url' => ['/softins/index']],
						/*['label' => 'Учет нового дистрибутива', 'url' => ['/soft/create']],
						['label' => 'Учет новой установленной лицензии', 'url' => ['/softins/create']],*/
					]
				],
				[
					'label' => '<span class="hidden-sm">Контракты</span>',
					'url' => ['/contr/index'],
					'options' => [
						'class' => (Yii::$app->controller->id == 'contr' || Yii::$app->controller->id == 'ldoc') ? 'active' : '',
						// 'class' => 'visible-lg',
					],
					/*'items' => [
						['label' => 'Контракты', 'url' => ['/contr/index']],
						['label' => 'Нормативно-правовые акты', 'url' => ['/ldoc/index']],
						['label' => 'Добавление контракта', 'url' => ['/contr/create']],
						['label' => 'Добавление НПА', 'url' => ['/ldoc/create']],
					]*/
				],
				[
					'label' => '<span class="visible-lg">События</span><span class="hidden-lg">Сб</span>',
					'url' => ['/event/index'],
					'options' => [
						'class' => (Yii::$app->controller->id == 'event') ? 'active' : '',
					],
					/*'items' => [
						['label' => 'Просмотр событий', 'url' => ['/event/index']],
						['label' => 'Добавление события', 'url' => ['/event/create']],
					]*/
				],
				[
					'label' => '<span class="visible-lg">Справочники</span><span class="hidden-lg">Сп</span>',
					'url' => ['/refs/index'], //будет подсветка только при /refs/index
					'options' => [
						'class' => (Yii::$app->controller->id == 'refs' || Yii::$app->controller->id == 'user') ? 'active' : '',
					],
					/*'items' => [
						 ['label' => 'Поиск организаций, ответственных лиц', 'url' => '#'],
						 ['label' => 'Поиск заявок, соглашений', 'url' => ['/refs/test']],
						 '<li class="divider"></li>',
						 ['label' => 'Редактирование справочников', 'url' => ['/refs/index']],
						 // '<li class="divider"></li>',
						 // '<li class="dropdown-header">Dropdown Header</li>',
						 // ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
					]*/
				],
				[
					'label' => 'От<span class="hidden-sm">четы</span>',
					'url' => ['/reps/index'], //будет подсветка только при /reps/index
					'options' => [
						'class' => (Yii::$app->controller->id == 'reps') ? 'active' : '',
					],
					'items' => [
						 ['label' => 'Ответственные за информационные системы', 'url' => ['/reps/index', 'id' => 'Resppers']],
						 ['label' => 'Используемые ресурсы', 'url' => ['/reps/index', 'id' => 'Usedres']],
						 //['label' => 'Используемые ресурсы на системе виртуализации', 'url' => ['/reps/index', 'id' => 'Usedresonvirt']],
						 ['label' => 'Используемые лицензии', 'url' => ['/reps/index', 'id' => 'Usedlicenses']],
						 //['label' => 'Состояние по подключению к системе ArcSight', 'url' => ['/reps/index', 'id' => 'Statearcsight']],
					]
				],
				[
					'label' => '<span style="color:#7BBBC7" class="hidden-sm">'.(Yii::$app->getUser()->id
						?
						Yii::$app->db->createCommand(
							'SELECT concat(
										last_name,
										\' \',
										substring(first_name, 1, 1),
										\'.\',
										substring(second_name, 1, 1),
										\'.\'
									) as user_param
							 FROM user WHERE id = :id')
		       				->bindValues(['id' => Yii::$app->getUser()->id])
		       				->queryOne()['user_param']
		       			:
		       				'Анонимный пользователь').'</span>',
					'url' => ['/admin'],
					'items' => [
						['label' => 'Личный кабинет', 'url' => ['/admin'], 'options' => ['title' => 'Личный кабинет', 'style' => 'color:red'], ],
						[
							'label' => '<span class="glyphicon glyphicon glyphicon-log-out"></span> Выход из системы', // для использования такой хрени ниже добавлено 'encodeLabels' => false,
							'url' => ['/site/logout'],
							'linkOptions' => ['data-method' => 'post'],
							'options' => [
								'title' => 'Выход из системы',
							]
						],
					]
				],
				// ['label' => 'About', 'url' => ['/site/about']],
				// ['label' => 'Contact', 'url' => ['/site/contact']],
			];
/*            if (Yii::$app->user->isGuest) {
				$menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
				$menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
			} else {
				$menuItems[] = [
					'label' => 'Logout (' . Yii::$app->user->identity->username . ')',
					'url' => ['/site/logout'],
					'linkOptions' => ['data-method' => 'post']
				];
			}*/
			echo '
				<form class="navbar-form navbar-left" role="search" action="">
					<input type="hidden" name="r" value="site/search">
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Поиск" name="searchstring" style="width:180px;">
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
