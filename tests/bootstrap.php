<?php

define('YII_ENABLE_EXCEPTION_HANDLER', false);
define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);

$_SERVER['HTTP_HOST'] = 'test';

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii/framework/yii.php');

$config = [
    'basePath' => __DIR__ . '/runtime',
    'components' => [
        'db' => [
            'connectionString' => 'sqlite::memory:',
        ],
        'cache' => [
            'class' => 'system.caching.CFileCache',
            'cachePath' => __DIR__ . '/runtime'
        ],
    ],
];

Yii::createWebApplication($config);
