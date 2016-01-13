<?php namespace Waavi\Tagging\Contracts;

interface TaggableInterface
{
    /**
     * Return collection of tags
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function tags();

    public function addTag($tagName);

    public function addTags($tagNames);

    public function setTags($tagNames);

    public function removeTag($tagName);

    public function removeAllTags();

    public function removeTags($tagNames);
}
