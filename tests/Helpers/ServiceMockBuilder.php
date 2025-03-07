<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\DTOs\BookDTO;
use App\Services\NYTBestSellersService;
use Mockery;

class ServiceMockBuilder
{
    public static function createBestSellersServiceMock(
        array $filters = [],
        ?array $returnData = null
    ): NYTBestSellersService {
        $defaultBook = new BookDTO(
            'Test Book',
            'Test Author',
            'Test Description',
            'Test Publisher',
            ['1234567890', '0987654321'],
            []
        );

        $mockService = Mockery::mock(NYTBestSellersService::class);

        $books = collect();

        if ($returnData === null) {
            $books->push($defaultBook);
        } else {
            foreach ($returnData as $bookData) {
                if ($bookData instanceof BookDTO) {
                    $books->push($bookData);
                } else {
                    $books->push(new BookDTO(
                        $bookData['title'] ?? null,
                        $bookData['author'] ?? null,
                        $bookData['description'] ?? null,
                        $bookData['publisher'] ?? null,
                        $bookData['isbn'] ?? [],
                        $bookData['ranks'] ?? []
                    ));
                }
            }
        }

        $mockService->shouldReceive('getBestSellers')
            ->once()
            ->with($filters)
            ->andReturn($books);

        return $mockService;
    }

    public static function createExceptionThrowingServiceMock(\Exception $exception): NYTBestSellersService
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->once()
            ->andThrow($exception);

        return $mockService;
    }

    public static function createEmptyResponseServiceMock(): NYTBestSellersService
    {
        $mockService = Mockery::mock(NYTBestSellersService::class);
        $mockService->shouldReceive('getBestSellers')
            ->andReturn(collect());

        return $mockService;
    }
}
