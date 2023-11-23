<?php

use Illuminate\Support\Facades\Route;

Route::prefix('hierarchies')->group(static function () {
    Route::get('/', 'HierarchyController@index');
    Route::post('/list', 'HierarchyController@postList');
    Route::post('/', 'HierarchyController@store');
    Route::get('/export', 'HierarchyController@export');
});