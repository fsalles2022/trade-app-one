<?php

Route::prefix('roles')->group(function () {
    Route::post('/list', 'RoleController@list');
    Route::get('/export', 'RoleController@export');
    Route::get('/{id}', 'RoleController@show')->where(['id' => '[0-9]+']);
    Route::post('/store', 'RoleController@store');
    Route::put('/edit/{id}', 'RoleController@edit')->where(['id' => '[0-9]+']);
    Route::get('/user/logged', 'RoleController@getUserRoleLogged');
    Route::get('/me/by-network', 'RoleController@rolesByNetwork');

    Route::get('/', 'RoleController@index');  /** Route used by the integrator */
});

Route::get('/permissions/me', 'RoleController@getPermissionsUserLogged');
