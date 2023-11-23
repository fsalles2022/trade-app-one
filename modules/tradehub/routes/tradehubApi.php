<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('tradehub')
    ->namespace('Tradehub\Http\Controllers')
    ->middleware(['api'])
    ->group(function () {
        Route::post('/send-portability-token', 'TradeHubController@sendPortabilityToken');
        Route::post('/send-portability-token-tim', 'TradeHubController@sendPortabilityTokenTim');
        Route::post('/check-portability-token', 'TradeHubController@checkPortabilityToken');
        Route::post('/sale/update', 'TradeHubController@receiveSaleUpdateByTradeHub');
    });
