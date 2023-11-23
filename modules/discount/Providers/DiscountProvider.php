<?php

namespace Discount\Providers;

use Illuminate\Support\ServiceProvider;

class DiscountProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'discount');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/discountApi.php');
    }
}
