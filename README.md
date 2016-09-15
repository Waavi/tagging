# Tagging - Tags for Laravel 5 Eloquent models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/waavi/tagging.svg?style=flat-square)](https://packagist.org/packages/waavi/tagging)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Waavi/tagging/master.svg?style=flat-square)](https://travis-ci.org/Waavi/tagging)
[![Total Downloads](https://img.shields.io/packagist/dt/waavi/tagging.svg?style=flat-square)](https://packagist.org/packages/waavi/tagging)

## Introduction

This package allows you to easily add tags to Eloquents models. Inspired by the handy [cviebrock/eloquent-taggable](https://github.com/cviebrock/eloquent-taggable) package.

WAAVI is a web development studio based in Madrid, Spain. You can learn more about us at [waavi.com](http://waavi.com)

## Laravel compatibility

 Laravel  | tagging
:---------|:----------
 5.1.x    | 1.0.x
 5.2.x    | 1.0.7 and higher
 5.3.x    | 2.0 and higher

## Installation

Require through composer

```shell
composer require waavi/tagging ^2.0
```

Or manually edit your composer.json file:

```shell
"require": {
    "waavi/tagging": "^2.0"
}
```

Add the following entry to the end of the providers array in app/config.php:

```php
Waavi\Tagging\TaggingServiceProvider::class,
```

If you do not use [Eloquent-Sluggable](https://github.com/cviebrock/eloquent-sluggable) or [Waavi\Translation](https://github.com/Waavi/translation) you will also need to add:

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

## Updating from version 1.x

Version 2.x is **not** backwards compatible with 1.x. You will need to fully remove v1, delete the migrations, and then re-install from scratch.

## Configuration

You may find the configuration file at `config/tagging`

```php
return [
    // Remove all the tag relations on model delete
    'on_delete_cascade' => true,
    // If you want your tag names to be translatable using waavi/translation, set to true.
    'translatable'      => false,
    // All tag names will be trimed and normalized using this function:
    'normalizer'        => 'mb_strtolower',
];
```

## Usage

Your models should implement Taggable's interface and use its trait:

```php
use Waavi\Tagging\Traits\Taggable;

class Post extends Model
{
    use Taggable;
}
```

Add tags to an **existing** model without removing existing ones:

```php
// Tag with a comma separated list of tags:
$model->tag('apple,orange');

// Tag with an array of tags:
$model->tag(['apple', 'orange']);
```

Replace existing tags by the given ones in an **existing** model:

```php
// Tag with a comma separated list of tags:
$model->retag('apple,orange');

// Tag with an array of tags:
$model->retag(['apple', 'orange']);
```

Remove tags from an **existing** model:

```php
// Remove tags with a comma separated list:
$model->untag('apple,orange');

// Remove tags with an array of tags:
$model->untag(['apple', 'orange']);
```

Remove all tags from an **existing** model:

```php
$model->detag();
```

Get tags:

```php
// As comma separated list:
$model->tagNames;

// As array ['apple', 'orange']:
$model->tagArray;

// Get a list of all of the tags ever applied to any model of the same class: ['apple', 'orange', 'strawberry']
$model->availableTags();
```

Get by tag:

```php
// Get entries that have ALL of the given tags:
$model->withAllTags('apple, orange');
$model->withAllTags(['apple', 'orange']);

// Get entries that have ANY of the given tags:
$model->withAnyTags('apple, orange');
$model->withAnyTags(['apple', 'orange']);

// Get a list of all of the tags ever applied to any model of the same class: ['apple', 'orange', 'strawberry']
$model->availableTags();
```
