<?php namespace Waavi\Tagging\Test;

use Waavi\Tagging\Contracts\TaggableRepositoryInterface;
use Waavi\Tagging\Repositories\Repository;
use Waavi\Tagging\Test\Post;
use Waavi\Tagging\Traits\TaggableRepository;

class PostRepository extends Repository implements TaggableRepositoryInterface
{
    use TaggableRepository;

    /**
     * The model being queried.
     *
     * @var \Waavi\Tagging\Models\Tag
     */
    protected $model;

    /**
     *  Constructor
     *  @param  \Waavi\Tagging\Models\Tag      $model  Bade model for queries.
     *  @param  \Illuminate\Validation\Validator        $validator  Validator factory
     *  @return void
     */
    public function __construct(Post $model)
    {
        $this->model = $model;
    }
}
