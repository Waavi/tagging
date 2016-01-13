<?php namespace Waavi\Tagging\Contracts;

interface TagRepositoryInterface
{
    public function findByName($name);

    public function create(array $attributes);

    public function findOrCreate($name);

    public function findOrCreateFromArray($namesArray);

    public function update(array $attributes);

    public function validate(array $attributes);

    public function validationErrors();

    public function deleteUnused();
}
