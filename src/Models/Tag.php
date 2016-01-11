<?php namespace Waavi\Tagging\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Waavi\Translation\Traits\Translatable;

class Tag extends Model
{
    //Traits
    use Translatable;

    protected $table = 'tagging_tags';

    public $fillable = ['name'];

    /**
     *  The following attributes will have translations managed automatically.
     *  See Translatable Trait
     *
     *  @var array
     */
    protected $translatableAttributes = ['name'];

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
     *  Insert a new tag into the database.
     *
     *  @param  string $name
     *  @throws InvalidTagException
     *  @return Tag
     */
    public static function build($name)
    {
        $tag = new static;
        return $tag->edit($name);
    }

    /**
     *  Edit and save the current tag.
     *
     *  @param  string $name
     *  @throws InvalidTagException
     *  @return Tag
     */
    public function edit($name)
    {
        $this->name = $name;
        return $this;
    }

    public function increment()
    {
        $this->count++;
        $this->save();
        return $this;
    }

    public function decrement()
    {
        $this->count--;
        $this->save();
        return $this;
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
