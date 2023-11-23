<?php

Route::prefix('import')->group(function () {
    Route::get('devices', 'ImportableController@getDevices');
}
);
