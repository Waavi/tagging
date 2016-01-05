<?php namespace Waavi\Tagging\Traits;

trait Taggable
{
    /**
     *  Register Model observer.
     *
     *  @return void
     */
    public static function bootTaggable()
    {
        static::observe(new TaggableObserver);
    }

}
