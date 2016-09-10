<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests/codeception');

$config = require(__DIR__ . '/common.php');

$config['components']['user'] = [
    'class' => 'app\components\app\ConsoleUser',
];

return $config;
