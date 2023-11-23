<?php

use Illuminate\Support\Facades\Route;

Route::prefix('points_of_sale')->group(function () {
    Route::post('/import', 'PointOfSaleController@postImport');
    Route::get('/import', 'PointOfSaleController@getImportModel');
    Route::post('/create', 'PointOfSaleController@store');
    Route::put('/edit/{cnpj}', 'PointOfSaleController@edit');
    Route::get('/', 'PointOfSaleController@index');
    Route::post('/list', 'PointOfSaleController@index');
    Route::get('/{cnpj}', 'PointOfSaleController@show');
    Route::post('/', 'PointOfSaleController@store');
    Route::get('/user/logged', 'PointOfSaleController@getUserPointOfSaleLogged');
    Route::put('/integration', 'PointOfSaleController@updateByIntegration');
});
