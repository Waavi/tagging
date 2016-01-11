<?php namespace Waavi\Tagging\Traits;

use Illuminate\Config\Repository as Config;
use Waavi\Tagging\Models\Tag;
use Waavi\Tagging\Repositories\TagRepository;

trait Taggable
{
    protected $taggingTags = [];

    /**
     *  Register Model observer.
     *
     *  @return void
     */
    public static function bootTaggable()
    {
        static::observe(new TaggableObserver);
    }

    /**
     * Return collection of tagged rows related to the tagged model
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function tags()
    {
        return $this->morphMany('Waavi\Tagging\Models\Tag', 'taggable');
    }

    /**
     * Adds a single tag
     *
     * @param $tagName string
     */
    private function addTag($tagName)
    {
        $tagName = trim($tagName);
        if (!$this->exists) {
            array_push($this->taggingTags, $tagName);
        } else {
            $tagRepository = \App::make(TagRepository::class);
            $tag           = $tagRepository->findOrCreate($tagName);
            $this->tags()->attach($tag->id);
            $tag->increment();
            // Launch event;
        }
    }

    /**
     * Adds a multiple tags
     *
     * @param $tagName string
     */
    private function addTags($tagNamesArray)
    {
        foreach ($tagNamesArray as $tagName) {
            $this->addTag($tagName);
        }
    }

    /**
     * Sync a multiple tags
     *
     * @param $tagName string
     */
    private function syncTags($tagNamesArray)
    {
        // Si no existe, sustituir array temporal
        // Si existe, remove todos los tags y añadir los del array. Comprobar la diferencia entre los que hay y los que quiers meter y borrar y meter solo los necesarios.
    }

    /**
     * Removes a single tag
     *
     * @param $tagName string
     */
    private function removeTag($tagName)
    {
        $tagName = trim($tagName);

        if (!$this->exists) {
            $this->taggingTags = array_diff($this->taggingTags, [$tagName]);
        } else {
            $tagRepository = \App::make(TagRepository::class);
            $tag           = $tagRepository->findByName($tagName);
            if ($tag) {
                $this->tags()->dettach($tag->id);
                $tag->decrement();
                if (Config::get('tagging.delete_unused_tags')) {
                    Tag::deleteUnused();
                }
                // Launch event;
            }
        }
    }

    /**
     * Remove a model tags
     *
     * @param $tagName string
     */
    private function removeAllTags()
    {
        $this->tags()->dettach();
    }

    /**
     * Remove a multiple tags
     *
     * @param $tagName string
     */
    private function removeTags($tagNamesArray)
    {
        foreach ($tagNamesArray as $tagName) {
            $this->removeTag($tagName);
        }
    }

}
