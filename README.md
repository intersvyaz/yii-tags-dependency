# yii-tags-dependency

[![Build Status](https://travis-ci.org/intersvyaz/yii-tags-dependency.svg?branch=master)](https://travis-ci.org/intersvyaz/yii-tags-dependency)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/intersvyaz/yii-tags-dependency/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/intersvyaz/yii-tags-dependency/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/intersvyaz/yii-tags-dependency/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/intersvyaz/yii-tags-dependency/?branch=master)


Verification of the cache relevance based on Dependency mechanism of Yii framework and tags, which are also stored in cache

Based on idea of Косыгин Александр < http://habrahabr.ru/users/kosalnik/ > described at http://habrahabr.ru/post/159079/

## Installation via Composer

php composer.phar require intersvyaz/yii-tags-dependency:*

## Configuration

1. This extension require configured cache


## Base Usage

```php
<?php

use Intersvyaz\Cache\TagsDependency;

$cache = \Yii::app()->cache;

// save any value into cache with this dependency
$cache->set('cacheKey', 'cacheValue', 0, new TagsDependency(['A', 'B']));

// check if there is a value in cache
var_dump($cache->get('cacheKey'));

// remove (invalidate) one or several tags
(new TagsDependency(['A']))->deleteTags();

// check if cached value is absent in cache
var_dump($cache->get('cacheKey'));
```

## CacheTagBehavior usage

```php
class TestActiveRecord extends \CActiveRecord
{
//    ...
    
    public function behaviors()
    {
        return [
            'cacheTagBehavior' => [
                'class' => CacheTagBehavior::class,
            ]
        ];
    }
    
//    ...
}

// in other code: 

$models = TestActiveRecord::model()->cacheTag(3600, $dependency)->findAll();

// ...

// read query from cache
$models = TestActiveRecord::model()->cacheTag(3600, $dependency)->findAll();

// ...
$model2 = TestActiveRecord::model()->findByPk(2);
$model2->title = 'test';
$model2->save();

// ...

// cache invalid, read query from db
$models = TestActiveRecord::model()->cacheTag(3600, $dependency)->findAll();
```
