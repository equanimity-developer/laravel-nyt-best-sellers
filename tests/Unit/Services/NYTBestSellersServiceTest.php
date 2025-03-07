<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTOs\BookDTO;
use App\Exceptions\NYTApiException;
use App\Services\NYTBestSellersService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class NYTBestSellersServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    public function test_get_best_sellers_makes_correct_api_request()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

        Http::fake([
            '*' => Http::response([
                'results' => [
                    [
                        'title' => 'Test Book',
                        'author' => 'Test Author',
                        'description' => 'Test Description',
                        'publisher' => 'Test Publisher',
                        'isbns' => [
                            ['isbn10' => '1234567890'],
                            ['isbn13' => '1234567890123'],
                        ],
                        'ranks_history' => [],
                    ]
                ]
            ], 200)
        ]);

        $service = new NYTBestSellersService();
        $result = $service->getBestSellers(['author' => 'Test Author']);

        Http::assertSent(function ($request) {
            return strpos($request->url(), '/lists/best-sellers/history.json') !== false &&
                   isset($request['author']) &&
                   $request['author'] === 'Test Author';
        });

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(BookDTO::class, $result->first());
        $this->assertEquals('Test Book', $result->first()->title);
        $this->assertEquals('Test Author', $result->first()->author);
        $this->assertEquals(['1234567890', '1234567890123'], $result->first()->isbn);
    }

    public function test_get_best_sellers_uses_cache()
    {
        $dto = new BookDTO(
            'Cached Book',
            'Cached Author',
            'Cached Description',
            'Cached Publisher',
            ['1234567890'],
            []
        );

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(collect([$dto]));

        $service = new NYTBestSellersService();
        $result = $service->getBestSellers();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(BookDTO::class, $result->first());
        $this->assertEquals('Cached Book', $result->first()->title);
        $this->assertEquals('Cached Author', $result->first()->author);
    }

    public function test_throws_nyt_api_exception_on_error()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

        Http::fake([
            '*' => Http::response([
                'fault' => [
                    'faultstring' => 'API Key invalid'
                ]
            ], 401)
        ]);

        $service = new NYTBestSellersService();
        $this->expectException(NYTApiException::class);
        $service->getBestSellers();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
