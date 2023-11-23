<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use SurfPernambucanas\Http\Controllers\SurfPernambucanasController;

Route::prefix('surfbr')
    ->middleware(['api'])
    ->group(function (): void {
        Route::post('/subscriber-activate', SurfPernambucanasController::class . '@subscriberActivate');
        Route::post('/allocate-msisdn', SurfPernambucanasController::class . '@allocateMsisdn');
        Route::post('/plans', SurfPernambucanasController::class . '@plans');
        Route::get('/activation-plans', SurfPernambucanasController::class . '@activationPlans');
        Route::get('/utils', SurfPernambucanasController::class . '@utils');
        Route::get('/domains', SurfPernambucanasController::class . '@domains');
        Route::get('/portin-date', SurfPernambucanasController::class . '@nextPortinDate');
    });
