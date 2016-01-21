<?php namespace Waavi\Tagging\Traits;

use Illuminate\Support\Str;

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
     * Return collection of tags
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tags()
    {
        $model = config('tagging.model');
        return $this->morphToMany($model, 'tagging_taggable');
    }

    /**
     * Filter model to subset with all given tags
     *
     * @param array or string $tagNames
     */
    public function scopeWithAllTags($query, $tagNames)
    {
        $tagNames = $this->tagsToArray($tagNames);
        $tagSlugs = $this->tagsNamesToTagSlugs($tagNames);

        $query->where(function ($q) use ($tagSlugs) {
            foreach ($tagSlugs as $tagSlug) {
                $q->whereHas('tags', function ($q) use ($tagSlug) {
                    $q->where('slug', 'like', $tagSlug);
                    if (config('tagging.uses_tags_for_different_models')) {
                        $q->where('taggable_type', 'like', get_class($this));
                    }
                });
            }
        });

        return $query;
    }

    /**
     * Filter model to subset with any given tags
     *
     * @param array or string $tagNames
     */
    public function scopeWithAnyTag($query, $tagNames)
    {
        $tagNames = $this->tagsToArray($tagNames);
        $tagSlugs = $this->tagsNamesToTagSlugs($tagNames);

        return $query->whereHas('tags', function ($q) use ($tagSlugs) {
            $q->whereIn('slug', $tagSlugs);
            if (config('tagging.uses_tags_for_different_models')) {
                $q->where('taggable_type', 'like', get_class($this));
            }
        });
    }

    /**
     * Adds a single tag
     *
     * @param string $tagName
     */
    public function addTag($tagName)
    {
        array_push($this->tagsToAdd, $tagName);
        return $this;
    }

    /**
     * Adds a multiple tags
     *
     * @param array or string $tagNames
     */
    public function addTags($tagNames)
    {
        $tagNames = $this->tagsToArray($tagNames);
        foreach ($tagNames as $tagName) {
            $this->addTag($tagName);
        }
        return $this;
    }

    /**
     * Set tags
     *
     * @param array or string $tagNames
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
        return $this;
    }

    /**
     * Removes a single tag
     *
     * @param string $tagName
     */
    public function removeTag($tagName)
    {
        array_push($this->tagsToRemove, $tagName);
        return $this;
    }

    /**
     * Remove all tags
     *
     */
    public function removeAllTags()
    {
        $currentTagNames = $this->tags->map(function ($item) {
            return $item->name;
        })->toArray();
        $this->removeTags($currentTagNames);
        return $this;
    }

    /**
     * Remove a multiple tags
     *
     * @param array or string $tagNames
     */
    public function removeTags($tagNames)
    {
        $tagNames = $this->tagsToArray($tagNames);
        foreach ($tagNames as $tagName) {
            $this->removeTag($tagName);
        }
        return $this;
    }

    /**
     * Converts input into array
     *
     * @param array or string $tagNames
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
        $tagNames = array_map('trim', $tagNames);
        return array_values($tagNames);
    }

    /**
     * Converts array of tag names in arrat of tag slugs
     *
     * @param array $tagNames
     * @return array
     */
    private function tagsNamesToTagSlugs($tagNames)
    {
        $tagSlugs = [];
        foreach ($tagNames as $tagName) {
            array_push($tagSlugs, Str::slug($tagName));
        }
        return $tagSlugs;
    }
}
