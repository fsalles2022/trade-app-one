<?php

use Core\HandBooks\Http\Controllers\HandbookController;
use Core\HandBooks\Http\Controllers\MockController;
use Illuminate\Support\Facades\Route;

Route::prefix('handbooks')->middleware(['api'])->group(function () {
    Route::get('/', HandbookController::class . '@index');
    Route::get('/paginated', HandbookController::class . '@paginate');
    Route::get('/{id}/edit', HandbookController::class . '@edit');
    Route::get('/{id}/show', HandbookController::class . '@show');
    Route::get('/domains', HandbookController::class . '@domains');

    Route::post('/', HandbookController::class . '@store');
    Route::post('/edit/{id}', HandbookController::class . '@update');
    Route::delete('/{id}', HandbookController::class . '@delete');
});
