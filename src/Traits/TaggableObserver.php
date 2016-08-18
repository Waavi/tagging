<?php namespace Waavi\Tagging\Traits;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Waavi\Tagging\Events\TagAdded;
use Waavi\Tagging\Events\TagRemoved;
use Waavi\Tagging\Repositories\TagRepository;

class TaggableObserver
{
    /**
     *  Save tags when model is saved.
     *
     *  @param  Model $model
     *  @return void
     */
    public function saved($model)
    {
        $tagRepository = \App::make(TagRepository::class);
        foreach ($model->tagsToAdd as $tagName) {
            $tags = $model->fresh()->tags->filter(function ($item) use ($tagName) {
                if ($item->slug == Str::slug($tagName)) {
                    return true;
                }
            });
            if ($tags->count() == 0) {
                $taggableType = null;
                if (config('tagging.uses_tags_for_different_models')) {
                    $taggableType = get_class($model);
                }
                $tag = $tagRepository->findOrCreate($tagName, $taggableType);
                if ($tag) {
                    $model->tags()->attach($tag->id);
                    $tag->increment('count', 1);
                    $tag->save();
                    Event::fire(new TagAdded($model));
                }
            }
        }
        foreach ($model->tagsToRemove as $tagName) {
            $tags = $model->fresh()->tags->filter(function ($item) use ($tagName) {
                if ($item->slug == Str::slug($tagName)) {
                    return true;
                }
            });
            if ($tags->count() === 1) {
                $tag = $tags->first();
                $model->tags()->detach($tag->id);
                $tag->decrement('count', 1);
                $tag->save();
                if (config('tagging.delete_unused_tags')) {
                    $tagRepository->deleteUnused();
                }
                Event::fire(new TagRemoved($model));
            }
        }
        $model->tagsToAdd = [];
        $model->tagsToAdd = [];
    }

    /**
     *  Delete tags when model is deleted.
     *
     *  @param  Model $model
     *  @return void
     */
    public function deleted($model)
    {
        if (config('tagging.remove_tags_on_delete')) {
            $tagRepository = \App::make(TagRepository::class);
            foreach ($model->tags as $tag) {
                $model->tags()->detach($tag->id);
                $tag->decrement('count', 1);
                $tag->save();
                if (config('tagging.delete_unused_tags')) {
                    $tagRepository->deleteUnused();
                }
                Event::fire(new TagRemoved($model));
            }
        }
    }
}
