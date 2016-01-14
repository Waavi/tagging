# Tagging - Laravel 5 - Tags for Eloquent models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/waavi/tagging.svg?style=flat-square)](https://packagist.org/packages/waavi/tagging)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/Waavi/tagging/master.svg?style=flat-square)](https://travis-ci.org/Waavi/tagging)
[![Total Downloads](https://img.shields.io/packagist/dt/waavi/tagging.svg?style=flat-square)](https://packagist.org/packages/waavi/tagging)

## Introduction

This package allows you to easily tagged eloquents models.

WAAVI is a web development studio based in Madrid, Spain. You can learn more about us at [waavi.com](http://waavi.com)

## Installation

Require through composer

    composer require waavi/tagging 1.0.x

Or manually edit your composer.json file:

    "require": {
        "waavi/tagging": "1.0.x"
    }

In config/app.php, add the following entry to the end of the providers array:

    Waavi\Tagging\TaggingServiceProvider::class,

If you not use Eloquent-Sluggable(https://github.com/cviebrock/eloquent-sluggable) or Waavi\Translation(https://github.com/Waavi/translation) add too:
    
    Cviebrock\EloquentSluggable\SluggableServiceProvider::class,
    Waavi\Translation\TranslationServiceProvider::class,    

Publish the configuration file:

    php artisan vendor:publish --provider="Waavi\Tagging\TaggingServiceProvider"

