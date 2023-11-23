<?php

Route::prefix('goals')->middleware(['api'])->group(function () {
    Route::prefix('import')->group(function () {
        Route::post('/month', 'Reports\Goals\Http\Controllers\GoalsController@import');
        Route::get('/month', 'Reports\Goals\Http\Controllers\GoalsController@example');
    });

    Route::post('export/month', 'Reports\Goals\Http\Controllers\GoalsController@export');

    Route::get('/', 'Reports\Goals\Http\Controllers\GoalsController@list');
});
