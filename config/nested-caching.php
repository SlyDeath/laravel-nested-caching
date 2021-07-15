<?php

return [
    
    // Force disable caching
    'disabled'      => env('DISABLE_CACHING', false),
    
    // Cache tag
    'cache-tag'     => 'nested-caching',
    
    // Environments where caching will be disabled
    'expelled-envs' => [
        'local',
    ],
];
