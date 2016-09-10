<?php

$config = require(__DIR__ . '/common/console.php');

$config['controllerNamespace'] = 'app\commands';

$config['enableCoreCommands'] = false;

return $config;
