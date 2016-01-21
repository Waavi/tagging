<?php namespace Waavi\Tagging\Contracts;

interface TaggableInterface
{
    public function tags();

    public function tagNamesToString();

    public function tagNamesToJson();

    public function tagNamesToArray();

    public function addTag($tagName);

    public function addTags($tagNames);

    public function setTags($tagNames);

    public function removeTag($tagName);

    public function removeAllTags();

    public function removeTags($tagNames);
}
