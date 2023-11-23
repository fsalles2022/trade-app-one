<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Terms\Http\Controllers\TermController;

Route::prefix('terms')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('/use', TermController::class . '@getTerm');
        Route::post('/use/accept', TermController::class . '@checkTerm');
    });
