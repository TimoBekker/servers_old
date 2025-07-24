<?php
$params = array_merge(
    // require(__DIR__ . '/../../common/config/params.php'),
    // require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
// ,require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'accservice-api',
//    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'basePath' => dirname(dirname(__DIR__)),
    'controllerNamespace' => 'app\api\controllers',
    'bootstrap' => [
        'log',
        'app\api\bootstrap\SetUp',
    ],
    'modules' => [],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/xml' => 'yii\web\XmlParser',
            ],
            'cookieValidationKey' => 'ask209almsol31f3',
        ],
        'response' => [
            'formatters' => [
                yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        'user' => [
            'identityClass' => 'app\api\models\Extern',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logFile' => dirname(__DIR__)."/runtime/api.log",
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=accservice_schema',
            'username' => 'accservice',
            'password' => '387af05c77',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'auth' => 'site/login',
                'GET profile' => 'profile/index',
                'PUT,PATCH profile' => 'profile/update',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'equipment',
                    'except' => ['delete'], // список роутов, которые не будем поддерживать. Соответствующие роутам методы вернут статус 405 Method not allowed
                    'extraPatterns' => [
                        'GET types' => 'types',
                        'GET states' => 'states',
                        'GET passes/<id:\d+>' => 'passwords',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'eresponses',
                    'except' => ['delete'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'responsibilities',
                    'except' => ['view','update','delete'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
                    'except' => ['view','update','delete'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'organization',
                    'except' => ['view','update','delete'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'ipaddress',
                    'except' => ['delete'],
                    'extraPatterns' => [
                        'GET vlans' => 'vlans',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'harddrives',
                    'except' => ['delete'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'software',
                    'except' => ['view','update','delete'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'esoftware',
                    'except' => ['delete'],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'infosystems',
                    'except' => ['view','update','delete'],
                    'extraPatterns' => [
                        'GET contours' => 'contours',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'einfosystems',
                    'except' => ['delete'],
                ],
            ],
        ],
    ],
    'params' => $params,
    'on '.yii\rest\ActiveController::EVENT_BEFORE_ACTION => function($event){
        ob_clean(); // очистка вывода, поскольку в вывод попадает тег пхп <?php<!xml....
    }
];
