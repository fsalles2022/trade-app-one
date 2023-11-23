<?php
use Illuminate\Support\Facades\Route;

/* No Auth Routes - Guest Middleware */
Route::get('/remote-payment/{token}', 'M4URemotePaymentController@index');
Route::get('/credit-card-remote-payment/{token}', 'CreditCardRemotePaymentController@getService');
Route::put('/credit-card-activation', 'CreditCardRemotePaymentController@putActivate');
