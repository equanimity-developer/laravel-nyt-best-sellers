<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Response;

class NYTApiException extends ApiException
{
    public function __construct(
        string $message = null,
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $errors = [],
        array $meta = [],
        \Throwable $previous = null
    ) {
        $message = $message ?? __('api.nyt.error');
        parent::__construct($message, $statusCode, $errors, $meta, $previous);
    }
} 