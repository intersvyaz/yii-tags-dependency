<?php
namespace Intersvyaz\Cache;

class CacheTagBehavior extends \CActiveRecordBehavior
{
    /**
     * Кеширование с тегом.
     * @param int $duration
     * @param TagsDependency $dependency
     * @return \CActiveRecord
     */
    public function cacheTag($duration, $dependency = null)
    {
        return $this->internalCache($duration, get_class($this->owner), $dependency);
    }

    /**
     * Кеширование конкретной модели по её PK
     * @param $duration
     * @param $pk
     * @param TagsDependency $dependency
     * @return \CActiveRecord
     */
    public function cacheTagByPk($duration, $pk, $dependency = null)
    {
        return $this->internalCache($duration, $this->getTagByPk($pk), $dependency);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($event)
    {
        $this->clearTags();
    }

    /**
     * @inheritdoc
     */
    public function afterDelete($event)
    {
        $this->clearTags();
    }

    /**
     * Функция принудительной очистки кеша, для всех моделей, и по возможности по pk
     * @param mixed $pk
     */
    public function clearTags($pk = null)
    {
        TagsDependency::clearTags([get_class($this->owner), $this->getTagByPk($pk)]);
    }

    /**
     * @param mixed $pk
     * @return string Имя тега, которое включает имя класса и PK модели (если $pk задан, то используется он)
     */
    public function getTagByPk($pk = null)
    {
        if (is_null($pk)) {
            $pk = $this->owner->getPrimaryKey();
        }

        return get_class($this->owner) . '.' . implode('.', (array)$pk);
    }

    private function internalCache($duration, $tag, $dependency = null)
    {
        $dependency = isset($dependency) ? $dependency : new TagsDependency([$tag]);
        return $this->owner->cache($duration, $dependency);
    }
}
