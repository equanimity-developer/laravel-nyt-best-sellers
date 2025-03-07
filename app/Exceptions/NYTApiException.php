<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Response;

class NYTApiException extends ApiException
{
    public function __construct(
        string $message = 'NYT API Error',
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $errors = [],
        array $meta = [],
        \Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errors, $meta, $previous);
    }
} 