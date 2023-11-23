<?php

Route::prefix('service')->group(function () {
    Route::post('edit/status', 'ServiceController@editStatusByContext');
    Route::put('edit', 'ServiceController@update');
    Route::get('status', 'ServiceController@statusListing');
    Route::post('available-services/update', 'ServiceController@updateAvailableServices');
});
