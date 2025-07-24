<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=accservice_schema',
    'username' => PROD_SERVER ? 'accservice' : 'root',
    'password' => PROD_SERVER ? '387af05c77' : 'root',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
