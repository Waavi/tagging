<?php

return [
    //Remove all the tags on model delete.
    'untag_on_delete'    => true,
    // Auto-delete unused tags from the 'tags' database table (when they are used zero times)
    'delete_unused_tags' => true,
];
