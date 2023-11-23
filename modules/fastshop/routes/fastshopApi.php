<?php

use Illuminate\Support\Facades\Route;
use \FastShop\Http\Controllers\ProductController;
use \FastShop\Http\Controllers\OrderController;
use \FastShop\Http\Controllers\CheckoutController;

Route::prefix('fastshop')
    ->middleware(['api'])
    ->group(static function () {

        Route::prefix('/products')
            ->group(static function () {
                Route::get('/', ProductController::class . '@products');
                Route::get('/{device}/pos/{pos}', ProductController::class . '@productSimulation');
            });

        Route::get('/orders/ddd/{ddd}/operator/{operator}/plan/{plan_id}/phone/{phone_slug}', OrderController::class . '@index');

        Route::get('/checkouts/{order}', CheckoutController::class . '@index');
        Route::post('/checkouts/{order}', CheckoutController::class . '@index');
    });
