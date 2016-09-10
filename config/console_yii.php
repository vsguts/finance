<?php

$config = require(__DIR__ . '/common/console.php');

// Remove Application
unset($config['components']['appBootstrap']);
if ($key = array_search('appBootstrap', $config['bootstrap'])) {
    unset($config['bootstrap'][$key]);
}

// Set gii
$config['bootstrap'][] = 'gii';
$config['modules']['gii'] = [
    'class' => 'yii\gii\Module',
];

return $config;
