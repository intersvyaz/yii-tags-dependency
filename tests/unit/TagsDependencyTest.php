<?php

use Intersvyaz\Cache\TagsDependency;

class TagsDependencyTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Yii::app()->cache->flush();
    }

    public function testSingleTagDependency()
    {
        /** @var \CCache $cache */
        $cache = \Yii::app()->cache;

        $key = 'key|' . uniqid() . '|1';
        $value = 'value|' . uniqid() . '|1';
        $tag = 'A';

        $cache->set($key, $value, 20, new TagsDependency([$tag]));
        $this->assertEquals($value, $cache->get($key));
        (new TagsDependency([$tag]))->deleteTags();
        $this->assertFalse($cache->get($key));
    }

    public function testMultipleTagDependency()
    {
        /** @var \CCache $cache */
        $cache = \Yii::app()->cache;

        $key = 'key|' . uniqid() . '|1';
        $value = 'value|' . uniqid() . '|1';
        $tags = ['A', 'B'];

        $cache->set($key, $value, 20, new TagsDependency($tags));
        $this->assertEquals($value, $cache->get($key));

        (new TagsDependency([$tags[0]]))->deleteTags();
        $this->assertFalse($cache->get($key));

    }
}
