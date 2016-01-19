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

    public $fillable = ['name'];

    protected $sluggable = [
        'build_from' => 'rawName',
        'save_to'    => 'slug',
    ];

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (function_exists('config') and config('tagging.uses_tags_for_different_models')) {
            $this->fillable            = ['name', 'taggable_type'];
            $this->sluggable['unique'] = false;
        }
        parent::__construct($attributes);
    }

    /**
     *  The following attributes will have translations managed automatically.
     *  See Translatable Trait
     *
     *  @var array
     */
    protected $translatableAttributes = ['name'];
}
