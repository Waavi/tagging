<?php namespace Waavi\Tagging\Models;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;
use Waavi\Tagging\Contracts\TagInterface;
use Waavi\Translation\Traits\Translatable;

class Tag extends Model implements SluggableInterface, TagInterface
{
    /**
     * Traits
     *
     */
    use Translatable;
    use SluggableTrait;

    protected $table = 'tagging_tags';

    public $timestamps = false;

    public $fillable = ['name'];

    protected $sluggable = [
        'build_from' => 'rawName',
        'save_to'    => 'slug',
    ];

    /**
     *  The following attributes will have translations managed automatically.
     *  See Translatable Trait
     *
     *  @var array
     */
    protected $translatableAttributes = ['name'];
}
