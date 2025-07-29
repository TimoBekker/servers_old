<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'accservice-backend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'your-secret-key-here',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                // API routes
                'POST api/auth' => 'api/auth/login',
                'POST api/logout' => 'api/auth/logout',
                'GET api/profile' => 'api/auth/profile',
                
                // Equipment API
                'GET api/equipment' => 'api/equipment/index',
                'POST api/equipment' => 'api/equipment/create',
                'GET api/equipment/<id:\d+>' => 'api/equipment/view',
                'PUT api/equipment/<id:\d+>' => 'api/equipment/update',
                'DELETE api/equipment/<id:\d+>' => 'api/equipment/delete',
                'GET api/equipment/types' => 'api/equipment/types',
                'GET api/equipment/states' => 'api/equipment/states',
                'GET api/equipment/passes/<id:\d+>' => 'api/equipment/passwords',
                
                // Information Systems API
                'GET api/infosystems' => 'api/infosystems/index',
                'POST api/infosystems' => 'api/infosystems/create',
                'GET api/infosystems/<id:\d+>' => 'api/infosystems/view',
                'PUT api/infosystems/<id:\d+>' => 'api/infosystems/update',
                'DELETE api/infosystems/<id:\d+>' => 'api/infosystems/delete',
                'GET api/infosystems/contours' => 'api/infosystems/contours',
                
                // Software API
                'GET api/software' => 'api/software/index',
                'POST api/software' => 'api/software/create',
                'GET api/software/<id:\d+>' => 'api/software/view',
                'PUT api/software/<id:\d+>' => 'api/software/update',
                'DELETE api/software/<id:\d+>' => 'api/software/delete',
                
                // Installed Software API
                'GET api/esoftware' => 'api/esoftware/index',
                'POST api/esoftware' => 'api/esoftware/create',
                'GET api/esoftware/<id:\d+>' => 'api/esoftware/view',
                'PUT api/esoftware/<id:\d+>' => 'api/esoftware/update',
                'DELETE api/esoftware/<id:\d+>' => 'api/esoftware/delete',
                
                // Dashboard API
                'GET api/dashboard/stats' => 'api/dashboard/stats',
                'GET api/dashboard/events' => 'api/dashboard/events',
                
                // CORS preflight
                'OPTIONS api/<action:.*>' => 'api/site/options',
            ],
        ],
        'response' => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
            'format' => \yii\web\Response::FORMAT_JSON,
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;