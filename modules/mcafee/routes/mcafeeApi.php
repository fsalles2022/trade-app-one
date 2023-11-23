<?php

use Illuminate\Support\Facades\Route;
use McAfee\Http\Controllers\McAfeeController;

Route::prefix('/sales/mcafee')->group(static function () {
    Route::middleware(['api', 'jwt'])->get('/plans', McAfeeController::class . '@plans');
});

Route::post('mcafee/subscription/internet', McAfeeController::class . '@onByInternet');

Route::post('sales-update/mcafee/{serviceTransaction}', McAfeeController::class . '@updateStatusPayment')->name('urlReturn');
