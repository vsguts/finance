<?php

$config = [
    'id' => 'app',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'appBootstrap'
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',

            // Need to set DSN

            // Cache
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'schemaCache' => 'cache',
        ],
        // 'redis' => [
        //     'class' => 'yii\redis\Connection',
        //     'port' => defined('REDIS_PORT') ? REDIS_PORT : 6379,
        // ],
        'cache' => [
            // 'class' => 'yii\redis\Cache',
            'class' => 'yii\caching\FileCache',
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
        'session' => [
            // 'class' => 'yii\redis\Session',
            'class' => 'yii\web\Session',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'formatter' => [
            'class' => 'app\components\app\Formatter',
            'locale' => 'en_US',
            'defaultTimeZone' => 'Europe/Minsk',
            'dateFormat' => 'dd.MM.yyyy',
            'datetimeFormat' => 'dd.MM.yyyy HH:mm',
            'decimalSeparator' => '.',
            'thousandSeparator' => ',',
            'currencyCode' => 'USD',
            'nullDisplay' => '',
        ],
        'security' => [
            'class' => 'app\components\app\Security',
            'derivationIterations' => 10,
        ],
        'authManager' => [
            'class' => 'app\components\rbac\DbManager',
            'defaultRoles' => ['guest', 'authorized'],
            'cache' => 'cache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Custom
                'currencies' => 'currency/index',
                'counterparties' => 'counterparty/index',
                'counterparty-categories' => 'counterparty-category/index',
                // Common
                '<controller:[\w\-]+>s' => '<controller>/index',
                '<controller:[\w\-]+>/<id:\d+>' => '<controller>/update',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    // 'fileMap' => [
                    //     'app' => 'app.php',
                    //     'app/error' => 'error.php',
                    // ],
                ],
            ],
        ],

        // Application components
        'appBootstrap' => [
            'class' => 'app\components\app\Bootstrap',
        ],
        'calendar' => [
            'class' => 'app\components\app\Calendar',
        ],
        'currency' => [
            'class' => 'app\components\app\Currency',
        ],
    ],
    'timeZone' => 'Europe/Minsk',
];

return $config;
