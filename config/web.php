<?php

$config = require(__DIR__ . '/common/common.php');

$config['components']['log']['traceLevel'] = YII_DEBUG ? 3 : 0;

$config['components']['request'] = [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '7UHHvisPXccbCuIgKg2dgRxhuJZD9CXJ',
];

$config['components']['user'] = [
    'identityClass' => 'app\models\User',
    'enableAutoLogin' => true,
];

$config['components']['errorHandler'] = [
    'errorAction' => 'site/error',
];

$config['components']['assetManager'] = [
    'converter' => [
        'class' => 'yii\web\AssetConverter',
    ],
    'appendTimestamp' => true,
    'linkAssets' => true,
];

$config['modules']['redactor'] = [
    'class' => 'app\modules\RedactorModule',
    'uploadDir' => '@webroot/images/uploads',
    'uploadUrl' => '@web/images/uploads',
    'imageAllowExtensions' => ['jpg', 'png', 'gif'],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '172.17.0.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '172.17.0.1'],
    ];
}

return $config;
