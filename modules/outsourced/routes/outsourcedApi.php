<?php

use Illuminate\Support\Facades\Route;

Route::prefix('customer')
    ->namespace('Outsourced\Http\Controllers')
    ->middleware(['api'])
    ->group(function (): void {
        Route::get('validate/{cpf}', 'OutsourcedCustomerController@validate');
    });

Route::prefix('outsourced')
    ->namespace('Outsourced\Http\Controllers')
    ->middleware(['api'])
    ->group(function (): void {
        Route::prefix('devices')->group(function (): void {
            Route::get('identifier/{identifier}', 'OutsourcedController@getDevice');
        });
    });
