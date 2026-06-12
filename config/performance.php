<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for performance optimizations
    | including cache durations, debounce delays, and other settings.
    |
    */

    'cache' => [
        'locations' => [
            'ttl' => 3600, // 1 hour
            'regions' => 7200, // 2 hours (rarely change)
        ],
        'members' => [
            'search_ttl' => 300, // 5 minutes
        ],
    ],

    'debounce' => [
        'location_search' => 300, // 300ms
        'member_search' => 500,   // 500ms
    ],

    'pagination' => [
        'default_per_page' => 25,
        'max_per_page' => 100,
    ],

    'database' => [
        'query_timeout' => 30,
        'connection_timeout' => 5,
    ],
];