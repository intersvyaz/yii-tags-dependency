<?php

namespace Intersvyaz\Cache;

use Serializable;

/**
 * Class Dependency
 * @package TaggedCache
 */
class TagsDependency implements \ICacheDependency, Serializable
{
    /**
     * List of tags
     * Array of string or Tag[]
     * @var string[]
     */
    private $tags = [];
    /**
     * List of tags versions
     * @var string[]
     */
    private $versions = [];
    /**
     * @var string name cache component
     */
    private $cacheName;
    /**
     * @var \CCache current cache component for store tags
     */
    private $cache;

    /**
     * @param array $tags
     * @param string $cacheName
     */
    public function __construct(array $tags, $cacheName = 'cache')
    {
        $this->tags = $tags;
        $this->cacheName = $cacheName;
    }

    /**
     * @inheritdoc
     */
    public function evaluateDependency()
    {
        $this->versions = $this->getTagsVersions();
    }

    /**
     * @inheritdoc
     */
    public function getHasChanged()
    {
        $currentVersions = $this->getTagsVersions(true);
        return !empty(array_diff_assoc($currentVersions, $this->versions));
    }

    /**
     * Delete tags
     */
    public function deleteTags()
    {
        foreach ($this->tags as $tag) {
            $this->getCache()->delete($this->getTagPrefixedName($tag));
        }
    }

    /**
     * @param array $tags
     * @param string $cacheName
     */
    public static function clearTags(array $tags, $cacheName = 'cache')
    {
        (new self($tags, $cacheName))->deleteTags();
    }

    /**
     * @param bool $onlyFromCache
     * @return array
     */
    private function getTagsVersions($onlyFromCache = false)
    {
        $versions = [];

        foreach ($this->tags as $tag) {
            $versions[$tag] = $this->getTagVersion($tag, $onlyFromCache);
        }

        return $versions;
    }

    /**
     * @param string $tag
     * @param bool $onlyFromCache
     * @return string
     */
    private function getTagVersion($tag, $onlyFromCache = false)
    {
        $version = $this->getCache()->get($this->getTagPrefixedName($tag));

        if (false === $version && !$onlyFromCache) {
            $version = $this->getNewTagVersion();
            $this->getCache()->set($this->getTagPrefixedName($tag), $version, 0 /* forever */);
        }

        return $version;
    }

    /**
     * @param string $tag
     * @return string
     */
    private function getTagPrefixedName($tag)
    {
        return '@' . self::class . '::' . (string)$tag;
    }

    /**
     * Returns new unique version string
     * @return string
     */
    private function getNewTagVersion()
    {
        return md5(uniqid() . getmypid() . mt_rand());
    }

    /**
     * @return \CCache
     */
    private function getCache()
    {
        if (!isset($this->cache)) {
            $this->cache = \Yii::app()->getComponent($this->cacheName);
        }

        return $this->cache;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize([$this->tags, $this->versions, $this->cacheName]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of the object.
     * @return void
     */
    public function unserialize($serialized)
    {
        list($this->tags, $this->versions, $this->cacheName) = unserialize($serialized);
    }
}
