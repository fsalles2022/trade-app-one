<?php

use Illuminate\Support\Facades\Route;
use Recommendation\Http\Controllers\RecommendationController;

Route::prefix('recommendations')
    ->middleware(['api'])
    ->group(static function () {
        Route::prefix('/import')
            ->group(static function () {
                Route::get('/', RecommendationController::class . '@getImportModel');
                Route::post('/', RecommendationController::class . '@import');
            });
        Route::get('/', RecommendationController::class . '@getRecommendation');
    });
