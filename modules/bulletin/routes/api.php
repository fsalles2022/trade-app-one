<?php

use Bulletin\Http\Controllers\BulletinController;
use Illuminate\Support\Facades\Route;

Route::prefix('bulletin')->namespace('Bulletin\Http\Controllers')->middleware(['api'])->group(function () {
    Route::put('activate/{bulletin}', 'BulletinController@activate');
    Route::put('confirm/{bulletin}', 'BulletinController@confirm');
    Route::get('user', 'BulletinController@getUserBulletin');
    Route::get('filters', 'BulletinController@filters');
});

Route::resource('bulletin', BulletinController::class)->middleware(['api']);
