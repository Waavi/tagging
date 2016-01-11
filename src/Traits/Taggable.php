<?php namespace Waavi\Tagging\Traits;

use Illuminate\Config\Repository as Config;
use Illuminate\Support\Facades\Event;
use Waavi\Tagging\Events\TagAdded;
use Waavi\Tagging\Events\TagRemoved;
use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Repositories\TagRepository;

trait Taggable
{
    protected $taggingTags = [];

    /**
     *  Register Model observer.
     *
     *  @return void
     */
    public static function bootTaggable()
    {
        static::observe(new TaggableObserver);
    }

    /**
     * Return collection of tagged rows related to the tagged model
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tags()
    {
        return $this->morphMany('Waavi\Tagging\Models\Tag', 'taggable');
    }

    /**
     * Adds a single tag
     *
     * @param $tagName string
     */
    private function addTag($tagName)
    {
        $tagName = trim($tagName);
        if (!$this->exists) {
            array_push($this->taggingTags, $tagName);
        } else {
            $tagRepository = \App::make(TagRepository::class);
            $tag           = $tagRepository->findOrCreate($tagName);
            $this->tags()->attach($tag->id);
            $tag->increment();
            Event::fire(new TagAdded($this));
        }
    }

    /**
     * Adds a multiple tags
     *
     * @param $tagName string
     */
    private function addTags($tagNames)
    {
        foreach ($tagNames as $tagName) {
            $this->addTag($tagName);
        }
    }

    /**
     * Sync a multiple tags
     *
     * @param $tagName string
     */
    private function syncTags($tagNames)
    {
        if (!$this->exists) {
            $this->taggingTags = $tagNames;
        } else {
            $currentTagNames = $this->tags->map(function ($item) {
                return $item->name;
            })->toArray();
            $deletions = array_diff($currentTagNames, $tagNames);
            $additions = array_diff($tagNames, $currentTagNames);
            $this->removeTags($deletions);
            $this->addTags($additions);
        }
    }

    /**
     * Removes a single tag
     *
     * @param $tagName string
     */
    private function removeTag($tagName)
    {
        $tagName = trim($tagName);

        if (!$this->exists) {
            $this->taggingTags = array_diff($this->taggingTags, [$tagName]);
        } else {
            $tagRepository = \App::make(TagRepository::class);
            $tag           = $tagRepository->findByName($tagName);
            if ($tag) {
                $this->tags()->dettach($tag->id);
                $tag->decrement();
                if (Config::get('tagging.delete_unused_tags')) {
                    Tag::deleteUnused();
                }
                Event::fire(new TagRemoved($this));
            }
        }
    }

    /**
     * Remove a model tags
     *
     * @param $tagName string
     */
    private function removeAllTags()
    {
        $this->tags()->dettach();
    }

    /**
     * Remove a multiple tags
     *
     * @param $tagName string
     */
    private function removeTags($tagNames)
    {
        foreach ($tagNames as $tagName) {
            $this->removeTag($tagName);
        }
    }

}
