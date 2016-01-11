<?php namespace Waavi\Tagging\Repositories;

use Illuminate\Config\Repository as Config;
use Illuminate\Validation\Factory as Validator;
use Waavi\Tagging\Models\Tag;

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
     *  Config repository.
     *
     *  @var Config
     */
    protected $config;

    /**
     *  Constructor
     *  @param  \Waavi\Tagging\Models\Tag      $model  Bade model for queries.
     *  @param  \Illuminate\Validation\Validator        $validator  Validator factory
     *  @return void
     */
    public function __construct(Tag $model, Validator $validator, Config $config)
    {
        $this->model     = $model;
        $this->validator = $validator;
        $this->config    = $config;
    }

    /**
     *
     *  @param  string   $name     Tag name
     *  @return boolean
     */
    public function findByName($name)
    {
        $slug = Str::slug(trim($name));
        return $this->model->where('slug', 'like', $slug)->first();
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
        return $this->validate($attributes) ? Tag::create($attributes) : null;
    }

    /**
     *  Insert a new tag entry into the database.
     *  If the attributes are not valid, a null response is given and the errors can be retrieved through validationErrors()
     *
     *  @param  string   $name     Tag name
     *  @return boolean
     */
    public function findOrCreate($name)
    {
        $slug = Str::slug(trim($name));
        $tag  = $this->model->where('slug', 'like', $slug)->first();
        if (!$tag) {
            if ($this->validate(['name' => $name])) {
                return false;
            }
            $tag = Tag::create($attributes);
        }
        return $this->validate($attributes) ? Tag::create($attributes) : null;
    }

    /**
     *  Insert a new tag entry into the database.
     *  If the attributes are not valid, a null response is given and the errors can be retrieved through validationErrors()
     *
     *  @param  array   $namesArray
     *  @return boolean
     */
    public function findOrCreateFromArray($namesArray)
    {
        $collection = new Collection;
        foreach ($namesArray as $name) {
            $tag = $this->findOrCreate($name);
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
        return $this->validate($attributes) ? (boolean) Tag::where('id', $attributes['id'])->update($attributes) : false;
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
            'name' => "required",
        ];
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
}
