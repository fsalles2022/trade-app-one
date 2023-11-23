<?php

Route::prefix('networks')->group(function () {
    Route::get('/', 'NetworkController@index');
    Route::post('/', 'NetworkController@store');
});
