<?php

return [
    'api' => [
        'endpoint' => env('NYT_API_ENDPOINT', '/lists/best-sellers/history.json'),
        'key' => env('NYT_API_KEY'),
        'base_url' => env('NYT_API_BASE_URL', 'https://api.nytimes.com/svc/books/v3'),
        'timeout' => env('NYT_API_TIMEOUT', 30),
        'retries' => env('NYT_API_RETRIES', 3),
        'retry_delay' => env('NYT_API_RETRY_DELAY', 1000),
    ],

    'cache' => [
        'ttl' => env('NYT_CACHE_TTL', 3600),
        'prefix' => 'nyt_best_sellers',
    ],
];
