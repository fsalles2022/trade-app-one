<?php

use Illuminate\Support\Facades\Route;

Route::get('collection/devices', 'DeviceController@getDevicesPaginated');
Route::get('devices', 'DeviceController@getDevices');
Route::post('devices/by-types', 'DeviceController@getDeviceFilteredByType');
Route::get('devices/types', 'DeviceController@getTypes');

Route::get('devices-network/import', 'DeviceNetworksController@getImportModel');
Route::post('devices-network/import', 'DeviceNetworksController@postImport');

Route::get('devices-outsourced', 'DeviceController@getDevicesOutsourced');
Route::get('devices-outsourced/import', 'DeviceOutSourcedController@importModel');
Route::post('devices-outsourced/import', 'DeviceOutSourcedController@import');
Route::get('devices-outsourced/export', 'DeviceOutSourcedController@exportData');
