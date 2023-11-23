<?php

use Illuminate\Support\Facades\Route;
use Reports\Http\Controllers\AnalyticalReportController;

Route::prefix('reports/analytical_report')->middleware(['api'])->group(function () {
    Route::post('/mobile_apps', AnalyticalReportController::class . '@analyticalMobileApplications');
    Route::post('/security_system', AnalyticalReportController::class . '@analyticalSecuritySystems');
    Route::post('/insurance_eletronics', AnalyticalReportController::class . '@analyticalInsuranceEletronics');
    Route::post('/external_sales', AnalyticalReportController::class . '@claroExternalSale');
});
Route::prefix('reports')->namespace('Reports\Http\Controllers')->middleware(['api'])->group(function () {
    Route::prefix('number')->group(function () {
        Route::post('total-sales', 'TotalSalesController@getSales');
        Route::post('total-month', 'TotalSalesPerMonthController@getSales');
        Route::post('total-day', 'TotalSalesPerDayController@getSales');
        Route::post('group-of-telecommunication-operations', 'MonthSalesByGroupOfOperationsController@getSales');
        Route::post('group-of-telecommunication-triangulations-total', 'TotalSalesTriangulationController@getSales');
        Route::post('group-of-telecommunication-operations-total', 'TotalSalesByGroupOfOperationsController@getSales');
        Route::post('group-of-telecommunication-operations-by-daily', 'DailySalesByGroupOfOperationsController@getSales');
    });

    Route::prefix('column')->group(function () {
        Route::post('top-5-total-hierarchy-sales', 'TopFiveHierarchyController@get');
        Route::post('top-5-regional', 'TopFiveRegionalController@get');
        Route::post('top-points-of-sale-by-operation', 'TopPointsOfSaleByOperationController@getSales');
        Route::post('sales-by-network-telecommunication', 'SalesByNetworkPlansTelecommunicationController@getSales');
    });

    Route::prefix('lines')->group(function () {
        Route::post('last-thirty-days-sales-per-operator', 'LastThirtyDaysSalesPerOperatorController@getSales');
        Route::post('hourly-sales-per-operator', 'HourlyOperatorController@getSales');
        Route::post('month-sales-per-operator', 'MonthSalesPerOperatorController@getSales');
        Route::post('month-sales-per-state', 'MonthSalesPerStateController@getSales');
        Route::post('tops-points-of-sales', 'TopPointOfSalesByOperatorController@getTop');
    });

    Route::prefix('donuts')->group(function () {
        Route::post('total-sales-per-operator', 'TotalSalesPerOperatorController@getSales');
        Route::post('total-sales-with-device', 'TotalSalesWithDeviceController@getSales');
    });

    Route::prefix('pie')->group(function () {
        Route::post('total-sales-by-claro', 'TotalSalesByClaroOperatorController@getSales');
        Route::post('total-sales-by-oi', 'TotalSalesByOiOperatorController@getSales');
        Route::post('total-sales-by-vivo', 'TotalSalesByVivoOperatorController@getSales');
        Route::post('total-sales-by-tim', 'TotalSalesByTimOperatorController@getSales');
        Route::post('total-sales-per-status', 'TotalSalesPerStatusController@getSales');
    });

    Route::prefix('third_parties')->group(function () {
        Route::post('sales', 'SalesToThirdPartiesController@index');
    });

    Route::get('filters', 'FiltersController@getFilters');
    Route::post('analytical_report', 'AnalyticalReportController@analyticalReport');

    Route::prefix('aggregated')->group(function () {
        Route::post('by-group-of-operations', 'AggregatedByGroupOfOperationsController@getSales');
    });

    Route::prefix('sales')->group(static function () {
        Route::post('/', 'SalesReportController@index');
    });

    Route::prefix('refused')->group(static function () {
        Route::get('/', 'RefusedSaleReportController@getRefused');
        Route::get('export', 'RefusedSaleReportController@exportCsv');
    });
});
