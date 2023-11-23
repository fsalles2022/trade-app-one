<?php

use Illuminate\Support\Facades\Route;

Route::prefix('timbr')
    ->namespace('TimBR\Http\Controllers')
    ->middleware(['api'])
    ->group(function () {
        Route::get('/domains', 'TimBRController@domains');
    });

Route::prefix('timbr')
    ->namespace('TimBR\Http\Controllers')
    ->middleware(['guest'])
    ->group(function () {
        Route::any('/auth/{network}', 'TimAuthController@networkAuthentication');
    });

Route::prefix('timbr')
    ->namespace('TimBR\Http\Controllers')
    ->middleware(['api'])
    ->group(static function () {
        Route::post('/eligibility', 'TimBRController@eligibility');
        Route::post('/checkMasterMsisdn', 'TimBRController@checkMasterMsisdn');
        Route::post('/creditAnalysis', 'TimBRController@creditAnalysis');
        Route::post('/simCard/activation', 'TimBRController@simCardActivation');
        Route::get('/cep', 'TimBRController@cep');
        Route::post('/register_credit_card', 'TimBRController@postRegisterCreditCard');
    });

Route::prefix('timbr/brscan')
    ->namespace('TimBR\Http\Controllers')
    ->middleware(['api'])
    ->group(static function () {
        Route::post('/generate-authenticate-link', 'BrScanController@generateAuthenticateLink');
        Route::post('/authenticate-status', 'BrScanController@getAuthenticateStatus');
        Route::post('/generate-sale-term-for-signature', 'BrScanController@generateSaleTermForSignature');
        Route::post('/sale-term-status', 'BrScanController@getSaleTermStatus');
    });

Route::prefix('timbr/rebate')
    ->namespace('TimBR\Http\Controllers')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('/import', 'TimBRController@getRebateImportableExampleAction');
        Route::post('/import', 'TimBRController@processRebateImportableAction');
    });
