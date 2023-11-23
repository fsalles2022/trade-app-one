<?php

Route::prefix('banners')->namespace('Banner\Http\BannerController')->middleware(['api'])->group(function () {
    Route::get('/', 'BannerController@index');
    Route::get('/admin', 'BannerController@admin');
    Route::post('/', 'BannerController@store');
    Route::put('/{id}', 'BannerController@edit');
    Route::put('/', 'BannerController@bulkEdit');
    Route::delete('/{id}', 'BannerController@destroy');
});
