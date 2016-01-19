<?php

return [
    // Remove all the tags relations on model delete (only if models uses softDeletes).
    'remove_tags_on_delete'          => true,
    // Delete unused tags from the 'tags' database table.
    'delete_unused_tags'             => true,
    // If you want to differentiate tags for differents models.
    // For example, ['8 ball', '9 ball'] tags for post models and ['8 ball', 'Pool championship'] tags for campionship models.
    // Each tags only uses for each models.
    'uses_tags_for_different_models' => false,
    // Model to use to store the tags in the database
    'model'                          => \Waavi\Tagging\Models\Tag::class,
];
