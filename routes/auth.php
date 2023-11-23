<?php

use Illuminate\Support\Facades\Route;

Route::post('/signin', 'Auth\AuthController@signin');

Route::get('/user/confirm/{verificationCode}', 'UserController@confirmVerificationCode');
Route::put('/user/confirm/{verificationCode}', 'UserController@confirmAccount');

Route::put('/user/activate/{verificationCode}', 'UserController@activateUser');

Route::post('/password_recovery', 'Auth\PasswordResetController@postRequestPasswordReset');

Route::prefix('/reset-password')->middleware(['api'])->group(static function () {
    Route::post('/', 'Auth\PasswordResetController@generateVerificationTokenForReset');
    Route::put('/', 'Auth\PasswordResetController@resetPasswordWithVerificationToken');
});


//Partner Authentication with AccessKey.
Route::post('/partner-authentication', 'Auth\PartnerAuthenticationController@grantAccess');