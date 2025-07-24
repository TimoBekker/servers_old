<?php
use yii\helpers\Html;
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
<body style="height:100%; background:url('img/bg.jpg') center no-repeat; background-size:cover;">
	<?php $this->beginBody() ?>
	<div class="wrap">
		<div class="container">
			<img src="img/mainlogo.png" alt="РС" style="float:left; margin:0 30px 0 0" width="100px">
			<h2 style="margin-top: 80px">Система учета серверов и информационных систем</h2>
		</div>
		<div class="container">
		<?= Alert::widget() ?>
		<?= $content ?>
		</div>
	</div>

	<footer class="footer navbar-fixed-bottom">
		<div class="container">
		<p class="pull-left">&copy; РЦУП <?= date('Y') ?></p>
		</div>
	</footer>

	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
