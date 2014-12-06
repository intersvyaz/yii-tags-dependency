<?php

define('YII_ENABLE_EXCEPTION_HANDLER', false);
define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);

$_SERVER['HTTP_HOST'] = 'test';

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii/framework/yii.php');

$config = [
    'basePath' => __DIR__ . '/runtime',
    'aliases' => [
        'fakes' => __DIR__ . '/fakes',
        'helpers' => __DIR__ . '/helpers',
    ],
    'import' => [
        'fakes.*',
        'helpers.*'
    ],
    'components' => [
        'cache' => [
            'class' => 'system.caching.CFileCache',
            'cachePath' => __DIR__.'/runtime',
        ],
    ],
];

Yii::createWebApplication($config);

// fix Yii's autoloader (https://github.com/yiisoft/yii/issues/1907)
Yii::$enableIncludePath = false;
Yii::import('fakes.*');
