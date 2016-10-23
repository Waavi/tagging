<?php

namespace Waavi\Tagging\Models\Translatable;

use Waavi\Tagging\Models\Tag as TagModel;
use Waavi\Translation\Traits\Translatable;

class Tag extends TagModel
{
    use Translatable;

    /**
     *  The following attributes will have translations managed automatically.
     *  @var array
     */
    protected $translatableAttributes = ['name'];

    /**
     * Return the sluggable configuration array for this model.
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'rawName',
                'unique' => true,
            ],
        ];
    }
}
