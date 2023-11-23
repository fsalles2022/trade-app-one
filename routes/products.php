<?php

use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/available_services', 'ProductController@getAvailableServices');
    Route::get('/', 'ProductController@getFilterProducts');
    Route::get('/formatted', 'ProductController@getAvailableServicesFormated');
});