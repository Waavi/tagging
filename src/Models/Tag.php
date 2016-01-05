<?php namespace Waavi\Tagging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $table = 'tagging_tags';

    public $fillable = ['name'];

    /**
     *  Set the name attribute.
     *  @param  string $value Name value
     *  @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
        if (!$this->exists) {
            $this->slug = $value;
        }
    }

    /**
     *  Set the slug attribute.
     *  @param  string $value Slug value
     *  @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Look at the tags table and delete any tags that are no londer in use by any taggable database rows.
     *
     * @return int
     */
    public static function deleteUnused()
    {
        return (new static )->newQuery()
            ->where('count', '=', 0)
            ->delete();
    }

}
