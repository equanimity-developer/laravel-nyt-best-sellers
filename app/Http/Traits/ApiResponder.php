<?php

declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponder
{
    protected function successResponse(
        array $data,
        string $message = null,
        int $code = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'status' => __('api.success'),
            'message' => $message,
            'data' => $data,
            'meta' => array_merge(['api_version' => $this->getApiVersion()], $meta),
        ], $code);
    }

    protected function errorResponse(
        string $message,
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $errors = [],
        array $meta = []
    ): JsonResponse {
        $response = [
            'status' => __('api.error'),
            'message' => $message,
            'meta' => array_merge(['api_version' => $this->getApiVersion()], $meta),
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function getApiVersion(): string
    {
        $routeName = request()->route()->getName();

        if (preg_match('/^api\.v(\d+)\./', $routeName, $matches)) {
            return 'v' . $matches[1];
        }

        return 'v1';
    }
}
