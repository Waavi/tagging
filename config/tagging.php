<?php

return [
    // Remove all the tags relations on model delete (only if models uses softDeletes).
    'remove_tags_on_delete' => true,
    // Delete unused tags from the 'tags' database table.
    'delete_unused_tags'    => true,
    // Model to use to store the tags in the database
    'model'                 => \Waavi\Tagging\Models\Tag::class,
];
