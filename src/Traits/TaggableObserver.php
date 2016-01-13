<?php namespace Waavi\Tagging\Traits;

use Illuminate\Support\Facades\Event;
use Waavi\Tagging\Events\TagAdded;
use Waavi\Tagging\Events\TagRemoved;
use Waavi\Tagging\Repositories\TagRepository;

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
        $tagRepository = \App::make(TagRepository::class);
        foreach ($model->tagsToAdd as $tagName) {
            $tag = $tagRepository->findOrCreate($tagName);
            $model->tags()->attach($tag->id);
            $tag->increment('count', 1)->save();
            $tag->save();
            Event::fire(new TagAdded($model));
        }
        foreach ($model->tagsToRemove as $tagName) {
            $tag = $tagRepository->findByName($tagName);
            if ($tag) {
                $model->tags()->dettach($tag->id);
                $tag->decrement('count', 1);
                $tag->save();
                if (config('tagging.delete_unused_tags')) {
                    $tagRepository->deleteUnused();
                }
                Event::fire(new TagRemoved($model));
            }
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
        if (config('tagging.remove_tags_on_delete')) {
            $model->removeAllTags();
        }
    }
}
