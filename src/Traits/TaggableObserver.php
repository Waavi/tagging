<?php namespace Waavi\Tagging\Traits;

use Illuminate\Config\Repository as Config;
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
        foreach ($tagsToAdd as $tagName) {
            $tag = $tagRepository->findOrCreate($tagName);
            $this->tags()->attach($tag->id);
            $tag->increment()->save();
            Event::fire(new TagAdded($this));
        }
        foreach ($tagsToRemove as $tagName) {
            $tag = $tagRepository->findByName($tagName);
            if ($tag) {
                $this->tags()->dettach($tag->id);
                $tag->decrement()->save();
                if (Config::get('tagging.delete_unused_tags')) {
                    $tagRepository->deleteUnused();
                }
                Event::fire(new TagRemoved($this));
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
        if (Config::get('tagging.remove_tags_on_delete')) {
            $this->removeAllTags();
        }
    }
}
