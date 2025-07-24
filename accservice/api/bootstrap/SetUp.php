<?php

namespace app\api\bootstrap;

use yii\base\BootstrapInterface;

/**
 * Created by PhpStorm.
 * User: custom
 * Date: 31.08.18
 * Time: 12:02
 */

class SetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;
        $container->set('yii\data\Pagination', ['pageSizeLimit' => [1,5000]]);
    }
}