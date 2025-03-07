<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\BestSellersController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->middleware(['throttle:api'])->group(function () {
    Route::get('/best-sellers', [BestSellersController::class, 'index'])
        ->name('best-sellers.index');

    Route::post('/best-sellers/search', [BestSellersController::class, 'search'])
        ->name('best-sellers.search');
});
