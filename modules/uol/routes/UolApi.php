<?php

use Illuminate\Support\Facades\Route;
use Uol\Http\Controllers\UolController;

Route::prefix('uol')->middleware(['api'])->group(function () {
    Route::get('/products', UolController::class . '@plans');
});
