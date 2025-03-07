<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Services\NYTBestSellersService;
use Illuminate\Http\Client\ConnectionException;
use Mockery;
use Tests\Helpers\ServiceMockBuilder;
use Tests\TestCase;
use Tests\Traits\AssertJsonResponseTrait;

class BestSellersEdgeCasesTest extends TestCase
{
    use AssertJsonResponseTrait;

    public function test_handles_connection_timeout()
    {
        $mockService = ServiceMockBuilder::createExceptionThrowingServiceMock(
            new ConnectionException('Connection timed out')
        );
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $this->assertFailedApiResponse($response);
    }

    public function test_handles_server_error()
    {
        $mockService = ServiceMockBuilder::createExceptionThrowingServiceMock(
            new \Exception('Internal Server Error', 500)
        );
        $this->app->instance(NYTBestSellersService::class, $mockService);

        // Make the API call
        $response = $this->getJson('/api/v1/best-sellers');

        $this->assertFailedApiResponse($response);
    }

    public function test_handles_unexpected_response_structure()
    {
        $mockService = ServiceMockBuilder::createEmptyResponseServiceMock();
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $this->assertSuccessfulBestSellersResponse($response);
        $response->assertJson([
            'data' => [
                'count' => 0,
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

        $response = $this->getJson('/api/v1/best-sellers?author='.$longAuthorName);

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

        $mockService = ServiceMockBuilder::createEmptyResponseServiceMock();
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $queryString = http_build_query(['isbn' => $tooManyIsbns]);
        $response = $this->getJson('/api/v1/best-sellers?'.$queryString);

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

        $response = $this->getJson('/api/v1/best-sellers?author='.$encodedChars);

        $response->assertStatus(200);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
