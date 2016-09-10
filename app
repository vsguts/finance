#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

file_exists($constants = __DIR__ . '/config/components/constants.php') && include($constants);
defined('YII_ENV') || define('YII_ENV', 'dev');

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/components/functions.php');

$config = require(__DIR__ . '/config/console_app.php');

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);
