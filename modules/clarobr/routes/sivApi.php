<?php

use Illuminate\Support\Facades\Route;

Route::prefix('sales')->group(function () {
    Route::prefix('siv')->namespace('ClaroBR\Http\Controllers')->middleware(['api'])->group(static function () {
        Route::get('/claro-list', 'SIVController@listClaroSales');
        Route::get('/', 'SIVController@utilsForCreateSale');
        Route::get('/plans', 'SIVController@plans');
        Route::get('/rebate', 'SIVController@rebate');
        Route::get('/rebate/devices', 'SIVController@devices');
        Route::post('/rebate/simulate', 'SIVController@simulateRebate');
        Route::put('log_sale', 'SIVController@logSale');
        Route::post('credit_analysis', 'SIVController@creditAnalysis');
        Route::post('analise-authenticate', 'SIVController@analiseAuthenticate');
        Route::get('status-authenticate', 'SIVController@statusAuthenticate');
        Route::post('save-status-authenticate', 'SIVController@saveStatusAuthenticate');
        Route::post('user-lines', 'SIVController@userLines');
    });
});

Route::prefix('siv/rebate')->namespace('ClaroBR\Http\Controllers')->middleware(['api'])->group(static function () {
    Route::get('/devices', 'SIVController@devices');
    Route::post('/simulate', 'SIVController@simulateRebate');
});

Route::prefix('clarobr')->namespace('ClaroBR\Http\Controllers')->middleware(['api'])->group(static function () {
    Route::post('/products', 'SIVController@products');
    Route::get('/domains', 'SIVController@domains');
});

Route::prefix('clarobr')->namespace('ClaroBR\Http\Controllers')->middleware(['api'])->group(static function () {
    Route::prefix('/automatic-registration')->group(function () {
        Route::post('/', 'SIVController@automaticRegistration');
        Route::get('/check-status', 'SIVController@checkAutomaticRegistrationStatus');
    });
});

Route::prefix('siv')->namespace('ClaroBR\Http\Controllers')->middleware(['api'])->group(static function () {
    Route::put('save_credentials', 'SIVController@saveCredentials');
    Route::get('/residential', 'SIVController@residential');
    Route::get('/{prefix}/iccid', 'SIVController@availableIccids');
});

Route::prefix('auth')->namespace('ClaroBR\Http\Controllers')->middleware(['signin'])->group(static function () {
    Route::post('/promoter', 'SIVController@promoterAuth');
});
