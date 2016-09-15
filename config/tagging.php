<?php

return [
    // Remove all the tags relations on model delete
    'on_delete_cascade' => true,
    // If you want your tag names to be translatable using waavi/translation, set to true.
    'translatable'      => false,
    // All tag names will be trimed and normalized using this function:
    'normalizer'        => 'mb_strtolower',
];
