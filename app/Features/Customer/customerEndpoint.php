<?php

use Illuminate\Support\Facades\Route;

Route::prefix('customer')->group(function () {
    Route::get('/{cpf}', '\TradeAppOne\Features\Customer\CustomerController@get');
});
