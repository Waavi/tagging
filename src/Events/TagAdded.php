<?php namespace Waavi\Tagging\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class TagAdded
{
    use SerializesModels;

    /** @var \Illuminate\Database\Eloquent\Model **/
    public $model;

    /**
     * Create a new event instance.
     *
     * @param  Model  $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
