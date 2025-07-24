<?php

use app\models\reference\SIpdnsTable;

require(__DIR__ . '/../../../vendor/autoload.php');
require(__DIR__ . '/../../../models/reference/SIpdnsTable.php');


class SIpdnsTableMock extends SIpdnsTable {
    public static function getDb() {
        $connection = new \yii\db\Connection([
            'dsn' => 'mysql:host=localhost;dbname=accservice_schema',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ]);
//        $connection->open();
        return $connection;
    }
}
