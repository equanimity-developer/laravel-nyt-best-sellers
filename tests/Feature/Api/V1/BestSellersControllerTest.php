<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Services\NYTBestSellersService;
use Mockery;
use Tests\Helpers\ServiceMockBuilder;
use Tests\TestCase;
use Tests\Traits\AssertJsonResponseTrait;

class BestSellersControllerTest extends TestCase
{
    use AssertJsonResponseTrait;

    public function test_index_returns_successful_response()
    {
        $mockService = ServiceMockBuilder::createBestSellersServiceMock();
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $this->assertSuccessfulBestSellersResponse($response);
        $response->assertJson([
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

        $mockService = ServiceMockBuilder::createBestSellersServiceMock($filters, [
            [
                'title' => 'Test Title',
                'author' => 'Test Author',
                'description' => 'Test Description',
                'publisher' => 'Test Publisher',
                'isbn' => ['1234567890', '1234567890123'],
                'ranks' => [],
            ]
        ]);
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers?'.http_build_query($filters));

        $this->assertSuccessfulBestSellersResponse($response, $filters);
        $response->assertJson([
            'data' => [
                'count' => 1,
            ],
        ]);
    }

    public function test_search_with_post_request()
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

        $mockService = ServiceMockBuilder::createBestSellersServiceMock($filters, [
            [
                'title' => 'Test Title',
                'author' => 'Test Author',
                'description' => 'Test Description',
                'publisher' => 'Test Publisher',
                'isbn' => ['1234567890', '1234567890123'],
                'ranks' => [],
            ]
        ]);
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->postJson('/api/v1/best-sellers/search', $filters);

        $this->assertSuccessfulBestSellersResponse($response, $filters);
        $response->assertJson([
            'data' => [
                'count' => 1,
            ],
        ]);
    }

    public function test_search_with_large_isbn_list()
    {
        $isbns = [];
        for ($i = 0; $i < 250; $i++) {
            $isbns[] = '0593803485';
            $isbns[] = '9780593803486';
        }

        $filters = [
            'author' => 'Test Author',
            'isbn' => $isbns,
            'offset' => 0,
        ];

        $mockService = ServiceMockBuilder::createEmptyResponseServiceMock();
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->postJson('/api/v1/best-sellers/search', $filters);

        $this->assertSuccessfulBestSellersResponse($response, $filters);
        $response->assertJson([
            'data' => [
                'count' => 0,
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
        $mockService = ServiceMockBuilder::createExceptionThrowingServiceMock(
            new \Exception('API Error')
        );
        $this->app->instance(NYTBestSellersService::class, $mockService);

        $response = $this->getJson('/api/v1/best-sellers');

        $this->assertFailedApiResponse($response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
