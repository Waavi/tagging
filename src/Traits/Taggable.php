<?php namespace Waavi\Tagging\Traits;

use Illuminate\Support\Str;
use Waavi\Tagging\Contracts\TagInterface;
use Waavi\Tagging\Models\Tag;

trait Taggable
{
    /**
     *  Register Model observer.
     *
     *  @return void
     */
    public static function bootTaggable()
    {
        static::deleted(function ($taggable) {
            if (config('tagging.on_delete_cascade')) {
                $taggable->detag();
            };
        });
    }

    /**
     * Return collection of tags
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tags()
    {
        return $this->morphToMany(get_class(app(TagInterface::class)), 'tagging_taggable');
    }

    /**
     * Return a string with all tags
     *
     * @return string
     */
    public function getTagNamesAttribute()
    {
        return $this->tags->implode('name', ',');
    }

    /**
     * Return an array with all tags
     *
     * @return array
     */
    public function getTagArrayAttribute()
    {
        return $this->tags->pluck('name')->toArray();
    }

    /**
     * Filter model to subset with all given tags
     *
     * @param array or string $tagNames
     */
    public function scopeWithAllTags($query, $tagNames)
    {
        $tagNames = is_array($tagNames) ? $tagNames : explode(',', $tagNames);
        return collect($tagNames)
        // Normalize names
        ->map(function ($tagName) {
            return Tag::normalizeTagName($tagName);
        })
        // Apply one where clause per tag
            ->reduce(function ($q, $tagName) {
                return $q->whereHas('tags', function ($subQuery) use ($tagName) {
                    $subQuery->where('name', $tagName);
                });
            }, $query);
    }

    /**
     * Filter model to subset with any given tags
     *
     * @param array or string $tagNames
     */
    public function scopeWithAnyTag($query, $tagNames)
    {
        $tagNames = is_array($tagNames) ? $tagNames : explode(',', $tagNames);
        $tagNames = collect($tagNames)
            ->map(function ($tagName) {
                return Tag::normalizeTagName($tagName);
            })
            ->toArray();

        return $query->whereHas('tags', function ($subQuery) use ($tagNames) {
            return $subQuery->whereIn('name', $tagNames);
        });
    }

    /**
     *  Return a Collection of all existing tags for this class.
     *
     *  @return \Illuminate\Database\Eloquent\Collection
     */
    public function availableTags()
    {
        return app(TagInterface::class)->join('tagging_taggables', function ($join) {
            $join->on('tagging_tags.id', '=', 'tag_id');
        })->where('tagging_taggable_type', '=', get_class($this))
            ->groupBy('slug')
            ->get()
            ->pluck('name')
            ->toArray();
    }

    /**
     *  Add the given tags to the model.
     *
     *  @param mixed $tagNames  Array or comma separated list of tags to apply to the model.
     */
    public function tag($tagNames)
    {
        $tagNames = is_array($tagNames) ? $tagNames : explode(',', $tagNames);
        $tagIds   = collect($tagNames)
            ->map(function ($tagName) {
                return app(TagInterface::class)->firstOrCreate([
                    'name' => Tag::normalizeTagName($tagName),
                ]);
            })
            ->pluck('id')
            ->toArray();
        $this->tags()->syncWithoutDetaching($tagIds);
        return $this->load('tags');
    }

    /**
     *  Removes the given tags from the model.
     *
     *  @param mixed $tagNames  Array or comma separated list of tags to apply to the model.
     */
    public function untag($tagNames)
    {
        $tagNames = collect(is_array($tagNames) ? $tagNames : explode(',', $tagNames))
            ->map(function ($tagName) {
                return Tag::normalizeTagName($tagName);
            })->toArray();
        $tags = app(TagInterface::class)->whereIn('name', $tagNames)->get()->pluck('id')->toArray();
        $this->tags()->detach($tags);
        return $this->load('tags');
    }

    /**
     *  Remove all existing tags and apply the given ones.
     *
     *  @param mixed $tagNames  Array or comma separated list of tags to apply to the model.
     *  @return void
     */
    public function retag($tagNames)
    {
        $this->detag();
        return $this->tag($tagNames);
    }

    /**
     *  Removes all tags
     *
     *  @param string $tagName
     */
    public function detag()
    {
        $this->tags()->sync([]);
        return $this->load('tags');
    }
}
