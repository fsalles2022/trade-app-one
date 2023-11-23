<?php

use Illuminate\Support\Facades\Route;
use Outsourced\ViaVarejo\Http\Controller\ViaVarejoCouponController;

Route::prefix('coupons')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('triangulation', ViaVarejoCouponController::class . '@getCoupon');
    });
