<?php

namespace Intersvyaz\Cache;

/**
 * Class Dependency
 * @package TaggedCache
 */
class TagsDependency implements \ICacheDependency
{
    /**
     * List of tags
     * Array of string or Tag[]
     * @var string[]
     */
    public $tags = [];
    /**
     * List of tags versions
     * @var string[]
     */
    public $versions = [];
    /**
     * @var string name cache component
     */
    public $cacheName;
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
        return '@' . get_class() . '::' . (string)$tag;
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
}
