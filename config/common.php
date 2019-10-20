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
            'dsn' => 'mysql:host=' . env('MYSQL_HOST') . ';dbname=' . env('MYSQL_DATABASE'),
            'username' => env('MYSQL_USER'),
            'password' => env('MYSQL_PASSWORD'),
            'charset' => 'utf8',

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

            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => env('MAILER_USE_FILE_TRANSPORT'),
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
            'defaultRoles' => ['role-guest', 'role-authorized'],
            'cache' => 'cache',
            'cacheKey' => 'rbac',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Common
                '<controlletBase:[\w\-]+>ies' => '<controlletBase>y/index',
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
