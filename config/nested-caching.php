<?php

return [
    
    // Force disable caching
    'disabled'        => env('DISABLE_CACHING', false),
    
    // Environments where caching will be disabled
    'expelled-envs'   => [
        'local',
    ],
    
    // Use another caching?
    'another-caching' => env('ENABLE_ANOTHER_CACHING', true),
    
    // Cache tag
    'cache-tag'       => 'nested-caching',
];
