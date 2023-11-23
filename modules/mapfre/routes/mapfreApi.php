<?php

Route::prefix('sales')->group(function () {
    Route::prefix('mapfre')->group(function () {
        Route::post('/', 'MapfreController@integrateService');
    });
});
;
