<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BestSellersRequest;
use App\Http\Resources\BookResource;
use App\Http\Traits\ApiResponder;
use App\Services\NYTBestSellersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
                    'best_sellers' => BookResource::collection($bestSellers),
                    'count' => $bestSellers->count(),
                ],
                __('api.best_sellers.retrieved'),
                Response::HTTP_OK,
                ['filters' => $filters]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                __('api.best_sellers.failed'),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['exception' => $e->getMessage()]
            );
        }
    }

    public function search(BestSellersRequest $request): JsonResponse
    {
        return $this->index($request);
    }
}
