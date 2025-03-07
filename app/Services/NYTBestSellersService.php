<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\BookDTO;
use App\Exceptions\NYTApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTBestSellersService
{
    private string $baseUrl;
    private string $endpoint;
    private string $apiKey;
    private int $cacheTtl;
    private string $cachePrefix;
    private int $timeout;
    private int $retries;
    private int $retryDelay;

    public function __construct()
    {
        $this->apiKey = config('nyt.api.key') ?: config('services.nyt.api_key');
        $this->baseUrl = config('nyt.api.base_url') ?: config('services.nyt.base_url');
        $this->endpoint = config('nyt.api.endpoint', '/lists/best-sellers/history.json');
        $this->cacheTtl = config('nyt.cache.ttl', 3600);
        $this->cachePrefix = config('nyt.cache.prefix', 'nyt_best_sellers');
        $this->timeout = config('nyt.api.timeout', 30);
        $this->retries = config('nyt.api.retries', 3);
        $this->retryDelay = config('nyt.api.retry_delay', 1000);
    }

    public function getBestSellers(array $filters = []): Collection
    {
        $cacheKey = $this->generateCacheKey($filters);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($filters) {
            $response = $this->makeRequest($filters);

            return $this->processResponse($response);
        });
    }

    protected function makeRequest(array $filters): array
    {
        try {
            $response = $this->client()
                ->get($this->endpoint, array_merge(
                    ['api-key' => $this->apiKey],
                    $this->prepareParameters($filters)
                ));

            if ($response->failed()) {
                Log::error(__('api.nyt.error'), [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new NYTApiException(
                    __('api.nyt.failed', ['status' => $response->status()]),
                    $response->status(),
                    ['response' => $response->json()],
                    ['filters' => $filters]
                );
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error(__('api.nyt.exception'), [
                'message' => $e->getMessage(),
                'filters' => $filters,
            ]);

            if ($e instanceof NYTApiException) {
                throw $e;
            }

            throw new NYTApiException(
                $e->getMessage(),
                500,
                [],
                ['filters' => $filters],
                $e
            );
        }
    }

    protected function processResponse(array $response): Collection
    {
        $results = $response['results'] ?? [];

        return collect($results)->map(function ($book) {
            return BookDTO::fromArray([
                'title' => $book['title'] ?? null,
                'author' => $book['author'] ?? null,
                'description' => $book['description'] ?? null,
                'publisher' => $book['publisher'] ?? null,
                'isbn' => $this->extractIsbns($book),
                'ranks' => $book['ranks_history'] ?? [],
            ]);
        });
    }

    protected function extractIsbns(array $book): array
    {
        $isbns = [];

        if (isset($book['isbns']) && is_array($book['isbns'])) {
            foreach ($book['isbns'] as $isbn) {
                if (isset($isbn['isbn10'])) {
                    $isbns[] = $isbn['isbn10'];
                }
                if (isset($isbn['isbn13'])) {
                    $isbns[] = $isbn['isbn13'];
                }
            }
        }

        return $isbns;
    }

    protected function prepareParameters(array $filters): array
    {
        return collect($filters)
            ->only(['author', 'title', 'offset', 'isbn'])
            ->filter()
            ->all();
    }

    protected function generateCacheKey(array $filters): string
    {
        return $this->cachePrefix . ':' . md5(json_encode($filters));
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retries, $this->retryDelay);
    }
}
