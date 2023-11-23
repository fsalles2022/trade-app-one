<?php

use Illuminate\Support\Facades\Route;
use Outsourced\Cea\Http\Controllers\CeaController;

Route::post('/import-gift-cards', CeaController::class . '@importGiftCards');
Route::get('/import-gift-cards', CeaController::class . '@importExample');
//Route::post('/activate-gift-card', CeaController::class . '@activateGiftCard');
