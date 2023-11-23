<?php

use Illuminate\Support\Facades\Route;

Route::prefix('sales')->group(static function () {
    Route::prefix('vivotradeup')
        ->namespace('VivoTradeUp\Http\Controllers')
        ->middleware(['api'])
        ->group(static function () {
            Route::put('/log_sale', 'VivoTradeupController@confirmControleCartao');
        });
});
