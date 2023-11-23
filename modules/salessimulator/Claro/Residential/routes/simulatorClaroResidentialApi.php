<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use SalesSimulator\Claro\Residential\Http\Controller\SalesSimulatorController;

Route::prefix('simulator')
    ->middleware(['api'])
    ->group(function (): void {
        Route::post('/residential-city-plans', SalesSimulatorController::class . '@getPlansAndPromotions');
    });
