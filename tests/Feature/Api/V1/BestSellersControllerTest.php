<?php

namespace Tests\Feature\Api\V1;

use App\Services\NYTBestSellersService;
use Mockery;
use Tests\TestCase;

class BestSellersControllerTest extends TestCase
{
    public function test_index_returns_successful_response()
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->once()
            ->with([])
            ->andReturn(collect([
                [
                    'title' => 'Test Book',
                    'author' => 'Test Author',
                    'description' => 'Test Description',
                    'publisher' => 'Test Publisher',
                    'isbn' => ['1234567890', '0987654321'],
                    'ranks' => [],
                ]
            ]));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'best_sellers',
                    'count',
                ],
                'meta' => [
                    'filters',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'count' => 1,
                ],
            ]);
    }

    public function test_index_with_filters()
    {
        $filters = [
            'author' => 'Test Author',
            'title' => 'Test Title',
            'isbn' => [
                '0593803485',
                '9780593803486'
            ],
            'offset' => 0,
        ];

        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->once()
            ->with($filters)
            ->andReturn(collect([
                [
                    'title' => 'Test Title',
                    'author' => 'Test Author',
                    'description' => 'Test Description',
                    'publisher' => 'Test Publisher',
                    'isbn' => ['1234567890', '1234567890123'],
                    'ranks' => [],
                ]
            ]));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers?'.http_build_query($filters));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'count' => 1,
                ],
                'meta' => [
                    'filters' => $filters,
                ],
            ]);
    }

    public function test_index_with_invalid_filters()
    {
        $filters = [
            'author' => str_repeat('a', 101),
            'isbn' => [
                '1231231230',
                '12312'
            ],
            'offset' => -1,
        ];

        $response = $this->getJson('/api/v1/best-sellers?'.http_build_query($filters));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['author', 'isbn.0', 'isbn.1', 'offset']);
    }

    public function test_index_handles_service_exception()
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->once()
            ->andThrow(new \Exception('API Error'));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to retrieve best sellers data',
            ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
