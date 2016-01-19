<?php namespace Waavi\Tagging\Contracts;

interface TagRepositoryInterface
{
    public function all($related = [], $perPage = 0, $taggableType = null);

    public function trashed($related = [], $perPage = 0, $taggableType = null);

    public function count($taggableType = null);

    public function findByName($name, $taggableType = null);

    public function findBySlug($slug, $taggableType = null);

    public function create(array $attributes);

    public function findOrCreate($name, $taggableType = null);

    public function findOrCreateFromArray($namesArray, $taggableType = null);

    public function update(array $attributes);

    public function validate(array $attributes);

    public function validationErrors();

    public function deleteUnused();
}
