<?php

return [
    //Remove all the tags on model delete.
    'remove_tags_on_delete' => true,
    // Auto-delete unused tags from the 'tags' database table (when they are used zero times)
    'delete_unused_tags'    => true,
    // Model to use to store the tags in the database
    'model'                 => 'Waavi\Tagging\Model\Tag',
];
