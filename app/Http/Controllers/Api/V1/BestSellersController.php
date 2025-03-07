<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BestSellersRequest;
use App\Http\Traits\ApiResponder;
use App\Services\NYTBestSellersService;
use Illuminate\Http\JsonResponse;

class BestSellersController extends Controller
{
    use ApiResponder;

    public function __construct(readonly private NYTBestSellersService $bestSellersService)
    {
    }

    public function index(BestSellersRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated();
            $bestSellers = $this->bestSellersService->getBestSellers($filters);

            return $this->successResponse(
                [
                    'best_sellers' => $bestSellers,
                    'count' => $bestSellers->count(),
                ],
                __('api.best_sellers.retrieved'),
                200,
                ['filters' => $filters]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                __('api.best_sellers.failed'),
                500,
                ['exception' => $e->getMessage()]
            );
        }
    }
}
