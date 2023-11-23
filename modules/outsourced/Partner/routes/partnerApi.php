<?php

use Illuminate\Support\Facades\Route;
use \Outsourced\Partner\Http\Controller\Auth\PartnerAuthenticationController;

Route::prefix('partner')->group(static function () {
    Route::post('authentication', PartnerAuthenticationController::class . '@grantAccess');
    Route::get('verify-token/{md5}', PartnerAuthenticationController::class . '@getCredentialByToken');
});
