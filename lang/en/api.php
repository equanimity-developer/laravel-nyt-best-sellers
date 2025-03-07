<?php

return [
    'success' => 'success',
    'error' => 'error',

    'nyt' => [
        'error' => 'NYT API Error',
        'exception' => 'NYT API Exception',
        'failed' => 'Failed to fetch data from NYT API: :status',
    ],

    'best_sellers' => [
        'retrieved' => 'Best sellers data retrieved successfully',
        'failed' => 'Failed to retrieve best sellers data',
    ],

    'validation' => [
        'isbn' => [
            'invalid' => 'The :attribute must be a valid ISBN (ISBN-10 or ISBN-13).',
            'array' => 'ISBN must be provided as an array',
            'string' => 'Each ISBN must be a string',
            'regex' => 'Each ISBN must be a valid 10-digit or 13-digit ISBN',
        ],
        'author' => [
            'string' => 'Author must be a text string',
            'max' => 'Author name cannot exceed :max characters',
        ],
        'title' => [
            'string' => 'Title must be a text string',
            'max' => 'Title cannot exceed :max characters',
        ],
        'offset' => [
            'integer' => 'Offset must be an integer',
            'min' => 'Offset cannot be negative',
            'max' => 'Offset cannot exceed :max',
        ],
    ],
];
