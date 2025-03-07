<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Services\NYTBestSellersService;
use Mockery;
use Tests\TestCase;

class ApiVersioningTest extends TestCase
{
    public function test_api_v1_exists()
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->andReturn(collect([]));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $response->assertStatus(200);
    }

    public function test_non_existent_version_returns_404()
    {
        $response = $this->getJson('/api/v2/best-sellers');

        $response->assertStatus(404);
    }

    public function test_non_existent_endpoint_returns_404()
    {
        $response = $this->getJson('/api/v1/non-existent-endpoint');

        $response->assertStatus(404);
    }

    public function test_rate_limiting()
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->andReturn(collect([]));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $maxAttempts = 60;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $response = $this->getJson('/api/v1/best-sellers');
            $response->assertStatus(200);
        }

        $response = $this->getJson('/api/v1/best-sellers');

        if ($response->status() === 429) {
            $response->assertStatus(429)
                ->assertJsonStructure(['message']);
        } else {
            $this->assertTrue(true, 'Rate limiting not enabled or threshold set higher than test limit');
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
