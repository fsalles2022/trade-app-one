<?php

use Illuminate\Support\Facades\Route;
use Integrators\Http\Controllers\ResidentialSaleImportController;

Route::prefix('integrators')->middleware(['api'])->group(static function () {
    Route::post('/residential', ResidentialSaleImportController::class . "@store");
    Route::put('/residential', ResidentialSaleImportController::class . "@update");
});
