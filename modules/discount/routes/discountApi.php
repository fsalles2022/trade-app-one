<?php

use Discount\Http\Controllers\DiscountController;
use Discount\Http\Controllers\UpdateImeiController;
use Illuminate\Support\Facades\Route;

Route::prefix('discounts')
    ->middleware(['api'])
    ->group(function () {
        Route::get('/', DiscountController::class . '@discounts');
        Route::post('/', DiscountController::class . '@create');
        Route::put('/switch-status/{id}', DiscountController::class . '@switchStatusAction');
        Route::put('/change-dates', DiscountController::class . '@changeDatesAction');
        Route::put('/{id}', DiscountController::class . '@update');
        Route::post('/{id}', DiscountController::class . '@getDiscount');
        Route::get('/available', DiscountController::class . '@discountsInSale');
        Route::get('/v2/available', DiscountController::class . '@discountsOrRebateInSale');
    });

Route::prefix('triangulations')
    ->middleware(['api'])
    ->group(function () {
        Route::post('/list', DiscountController::class . '@discounts');
        Route::post('/simulation', DiscountController::class . '@simulation');
        Route::get('/devices-available', DiscountController::class . '@devicesAvailable');
        Route::delete('/{id}', DiscountController::class . '@destroy');
    });

Route::prefix('imei')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('/', UpdateImeiController::class . '@getImei');
        Route::post('/authorization', UpdateImeiController::class . '@authorizeUpdateImei');
        Route::put('/', UpdateImeiController::class . '@updateImei');
    });

Route::get('brands-outsourced', DiscountController::class . '@getBrandsOutsourced')->middleware(['api']);
