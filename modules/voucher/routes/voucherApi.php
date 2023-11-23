<?php

use Illuminate\Support\Facades\Route;
use Voucher\Http\Controllers\VoucherController;

Route::prefix('vouchers')->middleware(['api'])->group(function () {
    Route::post('/', VoucherController::class . '@useDiscount');
    Route::get('{cpf}/availables', VoucherController::class . '@availableDiscounts');
    Route::get('{serviceTransaction}/check', VoucherController::class . '@checkVoucherIsAvailable');
    Route::put('{transaction}/cancel-without-chargeback', VoucherController::class . '@cancelWithoutChargeback');
    Route::put('{transaction}/cancel-with-chargeback', VoucherController::class . '@cancelWithChargeback');
    Route::get('{transaction}/aparelho/{imei}', VoucherController::class . '@getDiscountToChangeDevice');
    Route::put('{transaction}/aparelho/{imei}', VoucherController::class . '@applyDiscountForDevice');
});
