<?php

use Generali\Http\Controllers\GeneraliController;
use Illuminate\Support\Facades\Route;

Route::prefix('generali/v1')->middleware(['api'])->group(static function () {
    Route::post('/insurance', GeneraliController::class . '@ticket');
    Route::get('/eligibility', GeneraliController::class . '@eligibility');
    Route::get('/interest', GeneraliController::class. '@interest');
    Route::get('/refund', GeneraliController::class . '@calcRefund');
    Route::put('/gateway-activation-notify', GeneraliController::class . '@updateInsurance');
});

Route::prefix('generali')->middleware(['api'])->group(static function () {
    Route::get('/coverage/{id}', GeneraliController::class . '@coverage');
    Route::get('/products', GeneraliController::class . '@products');
    Route::get('/plans', GeneraliController::class . '@plans');
});
