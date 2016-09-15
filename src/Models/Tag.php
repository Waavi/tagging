<?php

namespace Waavi\Tagging\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Waavi\Tagging\Contracts\TagInterface;

class Tag extends Model implements TagInterface
{
    use Sluggable;

    /**
     * @var string
     */
    protected $table = 'tagging_tags';

    /**
     * @var array
     */
    public $fillable = ['name'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
                'unique' => true,
            ],
        ];
    }

    /**
     *  Mutator for the name attribute
     *
     *  @param  string $value
     *  @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Tag::normalizeTagName($value);
    }

    /**
     *  Show only tags belonging to the given class.
     *
     *  @param  \Illuminate\Database\Eloquent\Builder $query
     *  @param  string  $classname
     *  @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByModel($query, $classname)
    {
        return $query->where('taggable_type', $classname);
    }

    /**
     *  Normalize a tag name
     *
     *  @param  string $tagName
     *  @return string
     */
    public static function normalizeTagName($tagName)
    {
        return call_user_func(config('tagging.normalizer'), trim($tagName));
    }
}
