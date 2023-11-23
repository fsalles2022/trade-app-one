<?php

use Illuminate\Support\Facades\Route;

Route::prefix('vivobr')->namespace('VivoBR\Http\Controllers')->middleware(['api'])->group(function () {
    Route::post('/products', 'SunController@products');
    Route::get('/domains', 'SunController@domains');
});

Route::prefix('sales')->group(function () {
    Route::prefix('sun')->namespace('VivoBR\Http\Controllers')->middleware(['api'])->group(function () {
        Route::put('/log_sale', 'SunController@confirmControleCartao');
        Route::post('/', 'SunController@integrateService');
        Route::get('/totalization/{cpf}', 'SunController@totalization');
        Route::put('/', 'VivoSaleController@update');
    });
});

Route::prefix('sun')->namespace('VivoBR\Http\Controllers')->middleware(['api'])->group(function () {
    Route::post('/users', 'SunController@postUser');
});

Route::prefix('management')->namespace('VivoBR\Http\Controllers')->middleware(['api'])->group(function () {
    Route::post('/user/sync/vivo', 'ManagementController@syncUser');
});
