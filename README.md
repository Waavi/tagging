# Tagging - Tags for Laravel 5 Eloquent models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/waavi/tagging.svg?style=flat-square)](https://packagist.org/packages/waavi/tagging)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Waavi/tagging/master.svg?style=flat-square)](https://travis-ci.org/Waavi/tagging)
[![Total Downloads](https://img.shields.io/packagist/dt/waavi/tagging.svg?style=flat-square)](https://packagist.org/packages/waavi/tagging)

## Introduction

This package allows you to easily tagged eloquents models.

WAAVI is a web development studio based in Madrid, Spain. You can learn more about us at [waavi.com](http://waavi.com)

## Installation

Require through composer

```shell
composer require waavi/tagging 1.0.x
```

Or manually edit your composer.json file:
    
```shell
"require": {
    "waavi/tagging": "1.0.x"
}
```

In config/app.php, add the following entry to the end of the providers array:

```php
Waavi\Tagging\TaggingServiceProvider::class,
```

If you not use Eloquent-Sluggable(https://github.com/cviebrock/eloquent-sluggable) or Waavi\Translation(https://github.com/Waavi/translation) add too:
    
```php
Cviebrock\EloquentSluggable\SluggableServiceProvider::class,
Waavi\Translation\TranslationServiceProvider::class,    
```

Publish the configuration file and run the migrations:

```bash
php artisan vendor:publish --provider="Waavi\Tagging\TaggingServiceProvider"
php artisan migrate
```

Now you can edit config/tagging.php with your settings.

## Usage

### Eloquent Models

Your models should implement Taggable's interface and use it's trait:

```php
use Waavi\Tagging\Contracts\TaggableInterface;
use Waavi\Tagging\Traits\Taggable;

class Post extends Model implements TaggableInterface
{
    use Taggable;
} 
```

### Model Repositories

Your repostories should extends of 'Waavi\Tagging\Repositories\Repository' implement TaggableRepository's interface and use it's trait:

```php
use Waavi\Tagging\Contracts\TaggableRepositoryInterface;
use Waavi\Tagging\Repositories\Repository;
use PostModel;
use Waavi\Tagging\Traits\TaggableRepository;

class PostRepository extends Repository implements TaggableRepositoryInterface
{
    use TaggableRepository;

    /**
     * The model being queried.
     *
     * @var PostModel
     */
    protected $model;

    /**
     *  Constructor
     *  @param  PostModel      $model  Bade model for queries.
     *  @param  \Illuminate\Validation\Validator        $validator  Validator factory
     *  @return void
     */
    public function __construct(PostModel $model)
    {
        $this->model = $model;
    }
}
```

## Usage

### Taggable trait

```php
$post = Post::with('tags')->first(); // eager loading

$post->addTag('8 Ball'); // Attach '8 Ball' tag
$post->addTags(['9 Ball','10 Ball']); // Add '9 Ball' and '10 Ball' tags, also you always can use a string, for example: '9 Ball, 10 Ball'
$post->setTags(['9 Ball','10 Ball']); // Add '9 Ball' and '10 Ball' tags and remove other tags for example '8 ball', also you always can use a string, for example: '9 Ball, 10 Ball'
$post->removeTag('9 ball'); // Remove '9 Ball' tag
$post->removeAllTags(); // Remove all tags
$post->removeTags(['9 Ball','10 Ball']); // Remove '9 Ball' and '10 Ball' tags, also you always can use a string, for example: '9 Ball, 10 Ball'

$post->tags; // Get collection of tags
$post->tagNamesToString(); // Get a string with all tags
$post->tagNamesToJson(); // Get a json with all tags
$post->tagNamesToArray(); // Get an array with all tags
Post::withAnyTag(['9 Ball','10 Ball'])->get(); // Get posts with any tag listed, also you always can use a string, for example: '9 Ball, 10 Ball'
Post::withAllTags(['9 Ball','10 Ball'])->get(); // Get posts with all the tags, also you always can use a string, for example: '9 Ball, 10 Ball'
```

### Tag Repository

```php
$tagRepository = \App::make(\Waavi\Tagging\Repositories\TagRepository::class);
$tagRepository->findByName('8 Ball'); // Get tag by name
$tagRepository->findBySlug('8-ball'); // Get tag by slug
$tagRepository->create(['name' => '8 ball']); //Create a tag
$tagRepository->findOrCreate('8-ball'); // Get tag by name or create a tag if not exists.
$tagRepository->findOrCreateFromArray(['9 Ball','10 Ball']); // Get a collection of tags by name, create a tag if not exists.
$tagRepository->update(['id' => '1', 'name' => '8 ball']); // Update a especific tag
$tagRepository->deleteUnused(); // Delete unused tags(tags with count is zero).

// View \Waavi\Tagging\Repositories\Repository class to discover another methods.
```

### TaggableRepository trait

```php

$postRepository = \App::make(PostRepository::class);
$postRepository->withAnyTag(['9 Ball','10 Ball'], ['tags', 'author'], 10); // Get posts with any tag listed, also you always can use a string, for example: '9 Ball, 10 Ball'
$postRepository->withAllTags(['9 Ball','10 Ball'], ['tags', 'author'], 10); // Get posts with all the tags, also you always can use a string, for example: '9 Ball, 10 Ball'

// View \Waavi\Tagging\Repositories\Repository class to discover another methods.

```

## Differentiate tags for differents models

If you want to differentiate tags for differents models. You must activate 'uses_tags_for_different_models' in tagging.php config. For example, ['8 ball', '9 ball'] tags for post models and ['8 ball', 'Pool championship'] tags for campionship models. Each tags only uses for each models.

### Tag Repository (Only class changes his methods.)

```php
$tagRepository = \App::make(\Waavi\Tagging\Repositories\TagRepository::class);
$tagRepository->all([], 10, 'Waavi/Models/Post'); // Get all tags for post models
$tagRepository->trashed([],  10, 'Waavi/Models/Post'); // Get deleted tsag for post models
$tagRepository->count('Waavi/Models/Post'); // Total tags for post models
$tagRepository->findByName('8 Ball', 'Waavi/Models/Post'); // Get tag by name for post models
$tagRepository->findBySlug('8-ball', 'Waavi/Models/Post'); // Get tag by slug for post models
$tagRepository->create(['name' => '8 ball', 'taggable_type' => 'Waavi/Models/Post']); //Create a tag for post model
$tagRepository->findOrCreate('8-ball', 'Waavi/Models/Post'); // Get tag by name or create a tag for post models if not exists.
$tagRepository->findOrCreateFromArray(['9 Ball','10 Ball'], 'Waavi/Models/Post'); // Get a collection of tags by name, create a tag for post models if not exists.
```