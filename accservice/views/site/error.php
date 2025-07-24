<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error">

    <h1><?php // = Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        <!-- The above error occurred while the Web server was processing your request. -->
        Вас запрос на сервер оказался некорректен.
    </p>
    <p>
        <!-- Please contact us if you think this is a server error. Thank you. -->
        <!-- Пожалуйста свяжитесь с нами и сообщите об этой ошибке. Спасибо Вам. -->
        Если Вы не понимаете, почему оказались на данной странице, пожалуйста
        свяжитесь с нами и сообщите об этом. Спасибо Вам.
    </p>

</div>
