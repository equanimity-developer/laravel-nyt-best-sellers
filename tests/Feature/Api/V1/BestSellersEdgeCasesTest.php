<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Services\NYTBestSellersService;
use Illuminate\Http\Client\ConnectionException;
use Mockery;
use Tests\TestCase;

class BestSellersEdgeCasesTest extends TestCase
{
    public function test_handles_connection_timeout()
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->andThrow(new ConnectionException('Connection timed out'));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => __('api.best_sellers.failed'),
            ]);
    }

    public function test_handles_server_error()
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->andThrow(new \Exception('Internal Server Error', 500));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => __('api.best_sellers.failed'),
            ]);
    }

    public function test_handles_unexpected_response_structure()
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->andReturn(collect());

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => __('api.best_sellers.retrieved'),
                'data' => [
                    'count' => 0,
                ],
                'meta' => [
                    'api_version' => 'v1',
                ],
            ]);
    }

    public function test_handles_max_offset()
    {
        $response = $this->getJson('/api/v1/best-sellers?offset=2000000');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['offset']);
    }

    public function test_handles_extremely_long_author_name()
    {
        $longAuthorName = str_repeat('a', 1000);

        $response = $this->getJson('/api/v1/best-sellers?author=' . $longAuthorName);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['author']);
    }

    public function test_handles_too_many_isbn_values()
    {
        $tooManyIsbns = [];
        for ($i = 0; $i < 50; $i++) {
            $tooManyIsbns[] = '0593803485';
            $tooManyIsbns[] = '9780593803486';
        }

        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->andReturn(collect([]));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $queryString = http_build_query(['isbn' => $tooManyIsbns]);

        $response = $this->getJson('/api/v1/best-sellers?' . $queryString);

        $response->assertStatus(200);
    }

    public function test_handles_special_characters_in_parameters()
    {
        $specialChars = 'Test & Author % $ @ !';
        $encodedChars = urlencode($specialChars);

        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->with(['author' => $specialChars])
            ->andReturn(collect([]));

        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers?author=' . $encodedChars);

        $response->assertStatus(200);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
