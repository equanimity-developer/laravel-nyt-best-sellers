<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait AssertJsonResponseTrait
{
    protected function assertSuccessfulBestSellersResponse(TestResponse $response, array $filters = []): void
    {
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'best_sellers',
                    'count',
                ],
                'meta' => [
                    'api_version',
                    'filters',
                ],
            ])
            ->assertJson([
                'status' => __('api.success'),
                'message' => __('api.best_sellers.retrieved'),
                'meta' => [
                    'api_version' => 'v1',
                ],
            ]);

        if (!empty($filters)) {
            $response->assertJson([
                'meta' => [
                    'filters' => $filters,
                ],
            ]);
        }
    }

    protected function assertFailedApiResponse(TestResponse $response, int $statusCode = 500): void
    {
        $response->assertStatus($statusCode)
            ->assertJson([
                'status' => __('api.error'),
                'message' => __('api.best_sellers.failed'),
            ]);
    }
}
