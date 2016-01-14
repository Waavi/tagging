<?php namespace Waavi\Tagging\Contracts;

interface TaggableRepositoryInterface
{
    public function withAnyTag($tagNames, $related = [], $perPage = 0);

    public function withAllTags($tagNames, $related = [], $perPage = 0);
}
