<?php namespace Waavi\Tagging\Contracts;

interface TagInterface
{
    public function sluggable();

    /**
     * @param $query
     * @param $classname
     */
    public function scopeByModel($query, $classname);

    /**
     * @param $tagName
     */
    public static function normalizeTagName($tagName);
}
