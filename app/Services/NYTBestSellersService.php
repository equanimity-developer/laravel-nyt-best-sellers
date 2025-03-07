<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTBestSellersService
{
    private string $baseUrl;
    private string $endpoint = '/lists/best-sellers/history.json'; //move to config
    private string $apiKey;
    private int $cacheTtl = 3600; //move to config

    public function __construct()
    {
        $this->apiKey = config('services.nyt.api_key');
        $this->baseUrl = config('services.nyt.base_url');
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
                Log::error('NYT API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                throw new \Exception('Failed to fetch data from NYT API: '.$response->status());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('NYT API Exception', [
                'message' => $e->getMessage(),
                'filters' => $filters,
            ]);

            throw $e;
        }
    }

    protected function processResponse(array $response): Collection
    {
        $results = $response['results'] ?? [];

        return collect($results)->map(function ($book) {
            return [
                'title' => $book['title'] ?? null,
                'author' => $book['author'] ?? null,
                'description' => $book['description'] ?? null,
                'publisher' => $book['publisher'] ?? null,
                'isbn' => $this->extractIsbns($book),
                'ranks' => $book['ranks_history'] ?? [],
            ];
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
        $params = [];

        $allowedFilters = [
            'author',
            'title',
            'offset',
        ];

        foreach ($allowedFilters as $filter) {
            if (isset($filters[$filter]) && !empty($filters[$filter])) {
                $params[$filter] = $filters[$filter];
            }
        }

        if (isset($filters['isbn']) && is_array($filters['isbn'])) {
            $isbns = [];

            foreach ($filters['isbn'] as $isbn) {
                if (isset($isbn['isbn10'])) {
                    $isbns[] = $isbn['isbn10'];
                }
                if (isset($isbn['isbn13'])) {
                    $isbns[] = $isbn['isbn13'];
                }
            }

            if (!empty($isbns)) {
                $params['isbn'] = implode(';', $isbns);
            }
        }

        return $params;
    }

    protected function generateCacheKey(array $filters): string
    {
        return 'nyt_best_sellers:'.md5(json_encode($filters));
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout(30)
            ->retry(3, 1000);
    }
}
