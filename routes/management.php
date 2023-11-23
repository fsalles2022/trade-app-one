<?php

use Illuminate\Support\Facades\Route;

Route::prefix('management')->namespace('Management')->group(function () {
    Route::post('user/personify', 'ManagementUserController@postPersonify');
    Route::post('user/disable', 'ManagementUserController@postDisable');
    Route::post('user/enable', 'ManagementUserController@postEnable');
    Route::put('network/{slug}/preferences', 'ManagementNetworkController@update');
    Route::get('points_of_sale/export', 'ManagementPointOfSaleController@export');
    Route::get('enable/service-options', 'ManagementOperatorServiceController@getImportModel');
    Route::post('enable/service-options', 'ManagementOperatorServiceController@postImportEnableServices');
    Route::get('operators-services', 'ManagementOperatorServiceController@getAllServices');
    Route::get('operators-options', 'ManagementOperatorServiceController@getAllOptions');
    Route::get('operators-options/export', 'ManagementOperatorServiceController@exportAllOptions');
    Route::get('operators-services-by-network/{networkId}', 'ManagementOperatorServiceController@getServicesAndOptionsByNetwork');
    Route::get('operators-services-by-point-of-sale/{pointOfSaleId}', 'ManagementOperatorServiceController@getServicesAndOptionsByPointOfSale');
});
