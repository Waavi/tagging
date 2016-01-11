<?php namespace Waavi\Tagging\Traits;

use Illuminate\Config\Repository as Config;

class TaggableObserver
{
    /**
     *  Save translations when model is saved.
     *
     *  @param  Model $model
     *  @return void
     */
    public function saved($model)
    {
        if (!$this->exists) {
            $this->addTags($taggingTags);
        }
    }

    /**
     *  Delete translations when model is deleted.
     *
     *  @param  Model $model
     *  @return void
     */
    public function deleted($model)
    {
        if (!$this->exists and Config::get('tagging.untag_on_delete')) {
            $this->removeAllTags();
        }

    }
}
