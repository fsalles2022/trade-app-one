<?php

declare(strict_types=1);

use Core\PowerBi\Http\Controllers\PowerBiController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')
    ->middleware(['checkPowerBiAvailability'])
    ->group(function (): void {
        Route::get('mcafee', PowerBiController::class . '@mcafee');
        Route::get('filters', PowerBiController::class . '@getFilters');
        Route::get('lads', PowerBiController::class . '@lads');
        Route::get('telephony', PowerBiController::class . '@telephony');
        Route::get('management', PowerBiController::class . '@management');
        Route::get('insurance', PowerBiController::class . '@insurance');
        Route::get('tradeIn', PowerBiController::class . '@tradeIn');
        Route::get('goals/cea', PowerBiController::class . '@goalsCea');
        Route::get('goals/riachuelo', PowerBiController::class . '@goalsRiachuelo');
        Route::get('sales/pernambucanas', PowerBiController::class . '@salesPernambucanas');
        Route::get('commission/tim', PowerBiController::class . '@commissionTim');
        Route::get('claro/marketShare', PowerBiController::class . '@claroMarketShare');
    });
