<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BestSellersRequest;
use App\Services\NYTBestSellersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BestSellersController extends Controller
{
    public function __construct(readonly private NYTBestSellersService $bestSellersService)
    {
    }

    public function index(BestSellersRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $bestSellers = $this->bestSellersService->getBestSellers($filters);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'best_sellers' => $bestSellers,
                    'count' => $bestSellers->count(),
                ],
                'meta' => [
                    'filters' => $filters,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve best sellers data',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
