<?php namespace Waavi\Tagging\Test;

use Waavi\Tagging\Contracts\TaggableRepositoryInterface;
use Waavi\Tagging\Repositories\Repository;
use Waavi\Tagging\Test\Expense;
use Waavi\Tagging\Traits\TaggableRepository;

class ExpenseRepository extends Repository implements TaggableRepositoryInterface
{
    use TaggableRepository;

    /**
     * The model being queried.
     *
     * @var \Waavi\Tagging\Test\Expense
     */
    protected $model;

    /**
     *  Constructor
     *  @param  \Waavi\Tagging\Test\Expense      $model  Bade model for queries.
     *  @param  \Illuminate\Validation\Validator        $validator  Validator factory
     *  @return void
     */
    public function __construct(Expense $model)
    {
        $this->model = $model;
    }
}
