<?php namespace Waavi\Tagging\Repositories;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Validation\Factory as Validator;
use Waavi\Tagging\Contracts\TagInterface;

class TagRepository extends Repository
{
    /**
     * The model being queried.
     *
     * @var \Waavi\Tagging\Models\Tag
     */
    protected $model;

    /**
     *  Validator
     *
     *  @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     *  Validation errors.
     *
     *  @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     *  Constructor
     *  @param  \Waavi\Tagging\Models\Tag      $model  Bade model for queries.
     *  @param  \Illuminate\Validation\Validator        $validator  Validator factory
     *  @return void
     */
    public function __construct(TagInterface $model, Validator $validator)
    {
        $this->model     = $model;
        $this->validator = $validator;
    }

    /**
     *  Retrieve all records.
     *
     *  @param array $related Related object to include.
     *  @param integer $perPage Number of records to retrieve per page. If zero the whole result set is returned.
     *  @return \Illuminate\Database\Eloquent\Model
     */
    public function all($related = [], $perPage = 0, $taggableType = null)
    {
        $results = $this->model->with($related)->orderBy('created_at', 'DESC');
        if (Config::get('tagging.uses_tags_for_different_models')) {
            $results = $results->where('taggable_type', 'like', $taggableType);
        }
        return $perPage ? $results->paginate($perPage) : $results->get();
    }

    /**
     *  Retrieve all trashed.
     *
     *  @param array $related Related object to include.
     *  @param integer $perPage Number of records to retrieve per page. If zero the whole result set is returned.
     *  @return \Illuminate\Database\Eloquent\Model
     */
    public function trashed($related = [], $perPage = 0, $taggableType = null)
    {
        $trashed = $this->model->onlyTrashed()->with($related);
        if (Config::get('tagging.uses_tags_for_different_models')) {
            $results = $results->where('taggable_type', 'like', $taggableType);
        }
        return $perPage ? $trashed->paginate($perPage) : $trashed->get();
    }

    /**
     *  Returns total number of entries in DB.
     *
     *  @return integer
     */
    public function count($taggableType = null)
    {
        if (Config::get('tagging.uses_tags_for_different_models')) {
            return $this->model->where('taggable_type', 'like', $taggableType)->count();
        }
        return $this->model->count();
    }

    /**
     *  Retrieve a single record by name.
     *
     *  @param  string   $name     Tag name
     *  @return boolean
     */
    public function findByName($name, $taggableType = null)
    {
        $slug = Str::slug(trim($name));
        return $this->findBySlug($slug, $taggableType);
    }

    /**
     *  Retrieve a single record by slug.
     *
     *  @param  string   $slug     Tag slug
     *  @return boolean
     */
    public function findBySlug($slug, $taggableType = null)
    {
        $model = $this->model->where('slug', 'like', $slug);
        if (Config::get('tagging.uses_tags_for_different_models')) {
            $model = $model->where('taggable_type', 'like', $taggableType);
        }
        return $model->first();
    }

    /**
     *  Insert a new tag entry into the database.
     *  If the attributes are not valid, a null response is given and the errors can be retrieved through validationErrors()
     *
     *  @param  array   $attributes     Model attributes
     *  @return boolean
     */
    public function create(array $attributes)
    {
        $model = $this->model;
        return $this->validate($attributes) ? $model->create($attributes) : null;
    }

    /**
     *  Insert a new tag entry into the database.
     *  If the attributes are not valid, a null response is given and the errors can be retrieved through validationErrors()
     *
     *  @param  string   $name     Tag name
     *  @param  string   $taggableType Type of taggable model. For example: 'posts' Table's name of the model Post
     *  @return boolean
     */
    public function findOrCreate($name, $taggableType = null)
    {
        $slug = Str::slug(trim($name));
        $tags = $this->model->where('slug', 'like', $slug);
        if (Config::get('tagging.uses_tags_for_different_models')) {
            $tags = $tags->where('taggable_type', 'like', $taggableType);
        }
        $tag = $tags->first();
        if ($tag) {
            return $tag;
        }
        $model = $this->model;
        if (Config::get('tagging.uses_tags_for_different_models')) {
            return $this->validate(['name' => $name, 'taggable_type' => $taggableType]) ? $model->create(['name' => $name, 'taggable_type' => $taggableType]) : null;
        }
        return $this->validate(['name' => $name]) ? $model->create(['name' => $name]) : null;
    }

    /**
     *  Insert a new tags entries into the database.
     *  If the attributes are not valid, a null response is given and the errors can be retrieved through validationErrors()
     *
     *  @param  array   $namesArray
     *  @param  string   $taggableType Type of taggable model. For example: 'posts' Table's name of the model Post
     *  @return boolean
     */
    public function findOrCreateFromArray($namesArray, $taggableType = null)
    {
        $collection = new Collection;
        foreach ($namesArray as $name) {
            $tag = $this->findOrCreate($name, $taggableType = null);
            if (!$tag) {
                return false;
            }
            $collection->add($tag);
        }
        return $collection;
    }

    /**
     *  Insert a new tag entry into the database.
     *  If the attributes are not valid, a null response is given and the errors can be retrieved through validationErrors()
     *
     *  @param  array   $attributes     Model attributes
     *  @return boolean
     */
    public function update(array $attributes)
    {
        $model = $this->model->where('id', $attributes['id'])->first();
        return $this->validate($attributes) ? (boolean) $model->update($attributes) : false;
    }

    /**
     *  Validate the given attributes
     *
     *  @param  array    $attributes
     *  @return boolean
     */
    public function validate(array $attributes)
    {
        $rules = [
            'name' => 'required',
        ];
        if (Config::get('tagging.uses_tags_for_different_models')) {
            $rules['taggable_type'] = 'required';
        }
        $validator = $this->validator->make($attributes, $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }
        return true;
    }

    /**
     *  Returns the validations errors of the last action executed.
     *
     *  @return \Illuminate\Support\MessageBag
     */
    public function validationErrors()
    {
        return $this->errors;
    }

    /**
     * Delete any tags that are no londer in use by any taggable model.
     *
     * @return int
     */
    public function deleteUnused()
    {
        return $this->model->where('count', '=', 0)->delete();
    }

    public function autofill($term, $taggableType = null)
    {
        $model = $this->model->where('name', 'like', "%{$term}%");
        if (Config::get('tagging.uses_tags_for_different_models')) {
            $model = $model->where('taggable_type', 'like', $taggableType);
        }
        return $model->get()->lists('name')->toArray();
    }
}
