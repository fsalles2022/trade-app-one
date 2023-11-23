<?php

use Illuminate\Support\Facades\Route;

Route::get('/me', 'Auth\AuthController@getAuthenticatedUser');
Route::get('/encrypted-me', 'Auth\AuthController@getAuthenticatedUserEncrypted');
Route::get('/signout', 'Auth\AuthController@signout');

require base_path('routes/points_of_sale.php');
require base_path('routes/networks.php');
require base_path('routes/roles.php');
require base_path('routes/management.php');
require base_path('routes/services.php');
require base_path('routes/export.php');
require base_path('app/Features/Customer/customerEndpoint.php');
require base_path('routes/importable.php');
require base_path('routes/products.php');
require base_path('routes/devices.php');
require base_path('routes/log.php');
require base_path('routes/hierarchies.php');

Route::get('cep', 'CepController@get');

Route::prefix('users')->group(function () {
    Route::put('/approve', 'UserController@approveUsers');
    Route::post('/create', 'UserController@create');
    Route::put('/edit/{cpf}', 'UserController@edit');
    Route::get('/show/{cpf}', 'UserController@show');
    Route::post('/automatic-registration', 'UserController@sendAutomaticRegistrationImportable');
    Route::get('/automatic-registration', 'UserController@getAutomaticRegistrationImportableExample');
    Route::post('/import-password-mass-update', 'UserController@processPasswordMassUpdateImportableAction');
    Route::get('/import-password-mass-update', 'UserController@getPasswordMassUpdateImportableExampleAction');
    Route::get('/status/list', 'UserController@getStatus');
    Route::post('/import', 'UsersImportController@import');
    Route::get('/import', 'UsersImportController@getImportModel');
    Route::post('/import-delete', 'UsersImportController@importDelete');
    Route::get('/import-delete', 'UsersImportController@getImportModelDelete');
    Route::post('/list', 'UserController@index');
    Route::get('/list/by-points-of-sale', 'UserController@listByPointOfSale');
    Route::post('/export', 'UsersExportController@export');
    Route::get('/import-history', 'ImportHistoryController@getHistory');
    Route::get('/import-history/download/{id}', 'ImportHistoryController@getFile');
});

Route::prefix('sales')->group( static function () {
    Route::get('/', 'SaleController@integratorsIndex');
    Route::post('/', 'SaleController@postSaveSale');
    Route::post('/list', 'SaleController@index');
    Route::put('/pre-sale', 'SaleController@putUpdatePreSale');
    Route::put('/', 'SaleController@putActivateSale');
    Route::put('/contest', 'SaleController@putContest');
    Route::post('/backoffice', 'BackOfficeController@store');
    Route::put('/cancel', 'SaleController@putCancel');
    Route::get('/options', 'SaleController@saleOptions');
    Route::get('/recommendation', 'RecommendationController@getRecommendation');
    Route::get('/importOiResidential', 'SaleController@getModelOiResidentialSale');
    Route::post('/importOiResidential', 'SaleController@postImportOiResidentialSale');

});

Route::prefix('password_recovery')->group(function () {
    Route::get('/', 'Auth\PasswordResetController@index');
    Route::post('/list', 'Auth\PasswordResetController@index');
    Route::put('/', 'Auth\PasswordResetController@putResponseRequestPasswordReset');
});

Route::prefix('banner')->namespace('Components')->middleware(['api'])->group(function () {
    Route::get('/', 'BannerController@index');
    Route::get('/admin', 'BannerController@admin');
    Route::post('/', 'BannerController@store');
    Route::put('/{id}', 'BannerController@edit');
    Route::put('/', 'BannerController@bulkEdit');
    Route::delete('/{id}', 'BannerController@destroy');
});
