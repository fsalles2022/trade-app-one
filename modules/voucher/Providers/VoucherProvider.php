<?php

namespace Voucher\Providers;

use Illuminate\Support\ServiceProvider;

class VoucherProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'voucher');
        $this->loadRoutesFrom(__DIR__ . '/../routes/voucherApi.php');
    }
}
