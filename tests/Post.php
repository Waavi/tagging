<?php namespace Waavi\Tagging\Test;

use Illuminate\Database\Eloquent\Model;
use Waavi\Tagging\Contracts\TaggableInterface;
use Waavi\Tagging\Traits\Taggable;

class Post extends Model implements TaggableInterface
{
    use Taggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'text'];
}
