<?php

namespace VivoTradeUp\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class VivoTradeUpProvider extends AuthServiceProvider
{

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'vivotradeup');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->mergeConfigFrom(__DIR__.'/../config/vivotradeup.php', 'vivotradeup');
        $this->loadRoutesFrom(__DIR__ . '/../routes/vivoTradeupApi.php');
    }
}
