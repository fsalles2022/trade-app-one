<?php

Route::prefix('nextelbr')->namespace('NextelBR\Http\Controllers')->middleware(['api'])->group(function () {
    Route::get('/domains', 'NextelBRController@getDomains');
    Route::post('/eligibility', 'NextelBRController@postEligibility');
    Route::post('/logm4u', 'NextelBRController@postLogM4u');
    Route::post('/validation-bank-data', 'NextelBRController@validationBankData');
});
