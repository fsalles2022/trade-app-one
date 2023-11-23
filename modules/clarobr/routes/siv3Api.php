<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('clarobr')
    ->namespace('ClaroBR\Http\Controllers')
    ->middleware(['api'])
    ->group(function (): void {
        Route::prefix('v3')->group(function (): void {
            Route::post('check-external-sale', 'ExternalSaleController@checkExternalSale');
            Route::post('create-external-sale', 'ExternalSaleController@createExternalSale');
            Route::post('/send-authorization', 'Siv3Controller@sendAuthorization');
            Route::post('/check-authorization', 'Siv3Controller@checkAuthorization');
            Route::get('/viability/{serviceTransaction}', 'ViabilityController@show');
        });
    });
