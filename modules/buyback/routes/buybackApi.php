<?php

use Buyback\Http\Controllers\WaybillController;
use Illuminate\Support\Facades\Route;

Route::prefix('analytical_report')->namespace('Buyback\Http\Controllers')->middleware(['api'])->group(function () {
    Route::post('buyback', 'BuybackExportsController@export');
    Route::get('trade-in-unified', 'BuybackExportsController@unified');
});

Route::prefix('buyback')->namespace('Buyback\Http\Controllers')->middleware(['api'])->group(function () {
    Route::get('/devices', 'BuybackController@devices');
    Route::get('/devices-evaluations', 'EvaluationController@getDevicesEvaluations');
    Route::get('/evaluations-export', 'EvaluationController@export');
    Route::get('/questions', 'BuybackController@questions');
    Route::post('/price', 'BuybackController@price');
    Route::post('/revaluation', 'BuybackSaleAssistanceController@revaluation');
    Route::get('/find-watch', 'BuybackController@findWatch');
    Route::get('/find-ipad', 'BuybackController@findIpad');

    Route::prefix('/offer_declined')->group(function () {
        Route::post('/', 'TradeInOfferDeclinedController@offerDeclinedByCustomer');
        Route::get('/', 'TradeInOfferDeclinedController@index');
        Route::get('/export', 'TradeInOfferDeclinedController@export');
    });

    Route::prefix('/voucher')->group(function () {
        Route::post('/', 'BuybackSaleAssistanceController@voucher');
        Route::put('/burn', 'BuybackSaleAssistanceController@burnVoucher');
    });

    Route::prefix('import')->group(function () {
        Route::post('/evaluation', 'EvaluationController@import');
        Route::get('/evaluation', 'EvaluationController@getImportModel');
        Route::post('/devices', 'BuybackController@importDevices');
        Route::post('/prices', 'BuybackController@importPrices');

        Route::post('/evaluation-bonus', 'EvaluationBonusController@import');
        Route::get('/evaluation-bonus', 'EvaluationBonusController@getImportModel');
    });

    Route::prefix('quiz')->group(function () {
        Route::get('/', 'QuizController@index');
        Route::get('/{id}', 'QuizController@show');
        Route::post('/', 'QuizController@store');
        Route::put('/{id}', 'QuizController@update');
    });
});

Route::prefix('waybill')->middleware(['api'])->group(function () {
    Route::post('/generate', WaybillController::class . '@generate');
    Route::post('/availables', WaybillController::class . '@getAvailable');
    Route::post('/check', WaybillController::class . '@checkDevice');
});
