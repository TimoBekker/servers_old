<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    /* добавить роль - Управление ответственными в оборудовании*/
    public function actionAddAuthEqResp($confirm)
    {
        Yii::$app->db->createCommand("
          INSERT INTO auth_item (name, type, description, alias)
          VALUES (
            'manage-responsepers-any-equip', 
            1, 
            'Управление ответственными любого оборудования. Позволяет управлять ответственными за оборудование через форму редактирования оборудования', 
            'Управление ответственными любого оборудования')
        ")->execute();
    }
}
