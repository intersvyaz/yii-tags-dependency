<?php

namespace Intersvyaz\Cache\tests;

use Intersvyaz\Cache\TagsDependency;
use Intersvyaz\Cache\tests\fakes\CacheTagBehaviorTestActiveRecord;

class CacheTagBehaviorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        \Yii::app()->cache->flush();
        // pdo and pdo_sqlite extensions are obligatory
        if (!extension_loaded('pdo') || !extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO and SQLite extensions are required.');
        }

        // open connection and create testing tables
        \Yii::app()->db->setActive(true);
        \Yii::app()->db->getPdoInstance()->exec(file_get_contents(__DIR__ . '/../data/test_behavior.sql'));
    }

    public function tearDown()
    {
        \Yii::app()->db->setActive(false);
        \Yii::app()->cache->flush();
    }

    public function testCacheTagWorking()
    {
        $dependency = new TagsDependency([CacheTagBehaviorTestActiveRecord::class]);

        $model1 = CacheTagBehaviorTestActiveRecord::model()->cacheTag(3600, $dependency)->findByPk(1);
        $this->assertFalse($dependency->getHasChanged());
        $model2 = CacheTagBehaviorTestActiveRecord::model()->cacheTag(3600, $dependency)->findByPk(2);
        $this->assertFalse($dependency->getHasChanged());

        $model2->title = 'test';
        $this->assertTrue($model2->save());
        $this->assertTrue($dependency->getHasChanged());

        $model2 = CacheTagBehaviorTestActiveRecord::model()->cacheTag(3600, $dependency)->findByPk(2);
        $this->assertEquals('test', $model2->title);
        $this->assertFalse($dependency->getHasChanged());

        $this->assertTrue($model1->delete());
        $this->assertTrue($dependency->getHasChanged());
    }

    public function testCacheTagByPk()
    {
        $dependency1 = new TagsDependency([CacheTagBehaviorTestActiveRecord::model()->getTagByPk(1)]);
        $model1 = CacheTagBehaviorTestActiveRecord::model()->cacheTagByPk(3600, 1, $dependency1)->findByPk(1);
        $this->assertNotNull($model1);

        $dependency2 = new TagsDependency([CacheTagBehaviorTestActiveRecord::model()->getTagByPk(2)]);
        $model2 = CacheTagBehaviorTestActiveRecord::model()->cacheTagByPk(3600, 2, $dependency2)->findByPk(2);
        $this->assertNotNull($model2);

        $this->assertFalse($dependency1->getHasChanged());
        $this->assertFalse($dependency2->getHasChanged());
        $model2->delete();
        $this->assertFalse($dependency1->getHasChanged());
        $this->assertTrue($dependency2->getHasChanged());
    }
}
