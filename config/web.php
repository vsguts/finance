<?php

$config = [
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '7UHHvisPXccbCuIgKg2dgRxhuJZD9CXJ',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'converter' => [
                'class' => 'yii\web\AssetConverter',
            ],
            'appendTimestamp' => true,
            // 'linkAssets' => true,
        ],
    ],
    'modules' => [
        'redactor' => [
            'class' => 'app\modules\RedactorModule',
            'uploadDir' => '@webroot/images/uploads',
            'uploadUrl' => '@web/images/uploads',
            'imageAllowExtensions' => ['jpg', 'png', 'gif'],
        ]
    ],
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
