<?php
namespace Intersvyaz\Cache\tests\fakes;

use Intersvyaz\Cache\CacheTagBehavior;

/**
 * @property integer $id
 * @property string $title
 * @property integer $created_at
 * @property integer $updated_at
 */
class CacheTagBehaviorTestActiveRecord extends \CActiveRecord
{
    /**
     * @param string $className
     * @return static
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'table';
    }

    public function behaviors()
    {
        return [
            'cacheTagBehavior' => [
                'class' => CacheTagBehavior::class,
            ]
        ];
    }
}
