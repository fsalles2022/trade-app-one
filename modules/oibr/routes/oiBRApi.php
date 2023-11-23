<?php

use Illuminate\Support\Facades\Route;

Route::prefix('oibr')->namespace('OiBR\Http\Controllers')->middleware(['api'])->group(function () {
    Route::post('/get_credit_card', 'OiBRController@getCreditCardsOfMsisdn');
    Route::post('/register_credit_card', 'OiBRController@postRegisterCreditCard');
    Route::get('/products', 'OiBRController@getPlans');
    Route::post('/eligibility', 'OiBRController@eligibility');
    
    Route::prefix('residential')->group(function (): void {
        Route::get('url', 'OiBRController@oiRedirect');
    });
});
