<?php

namespace Waavi\Tagging\Models;

use Waavi\Translation\Traits\Translatable;

class TranslatableTag extends Tag
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
