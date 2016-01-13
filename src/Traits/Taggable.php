<?php namespace Waavi\Tagging\Traits;

trait Taggable
{
    public $tagsToAdd    = [];
    public $tagsToRemove = [];

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
        $model = config('tagging.model');
        return $this->morphMany($model, 'taggable');
    }

    /**
     * Adds a single tag
     *
     * @param $tagName string
     */
    public function addTag($tagName)
    {
        array_push($this->taggingToAdd, $tagName);
    }

    /**
     * Adds a multiple tags
     *
     * @param $tagName string
     */
    public function addTags($tagNames)
    {
        $tagNames = $this->tagsToArray($tagNames);
        foreach ($tagNames as $tagName) {
            $this->addTag($tagName);
        }
    }

    /**
     * Set tags
     *
     * @param $tagName string
     */
    public function setTags($tagNames)
    {
        $tagNames        = $this->tagsToArray($tagNames);
        $currentTagNames = $this->tags->map(function ($item) {
            return $item->name;
        })->toArray();
        $deletions = array_diff($currentTagNames, $tagNames);
        $additions = array_diff($tagNames, $currentTagNames);
        $this->removeTags($deletions);
        $this->addTags($additions);
    }

    /**
     * Removes a single tag
     *
     * @param $tagName string
     */
    public function removeTag($tagName)
    {
        array_push($this->tagsToRemove, $tagName);
    }

    /**
     * Remove all tags
     *
     * @param $tagName string
     */
    public function removeAllTags()
    {
        $this->tags->each(function ($tag) {
            $tag->decrement()->save();
        });
        $this->tags()->dettach();
    }

    /**
     * Remove a multiple tags
     *
     * @param $tagName string
     */
    public function removeTags($tagNames)
    {
        $tagNames = $this->tagsToArray($tagNames);
        foreach ($tagNames as $tagName) {
            $this->removeTag($tagName);
        }
    }

    /**
     * Converts input into array
     *
     * @param $tagNames string or array
     * @return array
     */
    private function tagsToArray($tagNames)
    {
        if (is_string($tagNames)) {
            $tagNames = explode(',', $tagNames);
        }

        if (!is_array($tagNames)) {
            return [];
        }

        $tagNames = reset($tagNames);
        $tagNames = array_map('trim', $tagNames);
        return array_values($tagNames);
    }
}
