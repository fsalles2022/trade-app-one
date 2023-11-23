<?php

namespace Banner\Providers;

use Illuminate\Support\ServiceProvider;

class BannerProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'banner');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }
}
