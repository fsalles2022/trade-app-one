<?php

declare(strict_types=1);

namespace Terms\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;

class TermServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadFactories();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');
        $this->loadRoutesFrom(__DIR__  . '/../routes/termsApi.php');
    }

    private function loadFactories(): void
    {
        if (Environments::PRODUCTION !== app()->environment()) {
            app(Factory::class)->load(__DIR__ . '/../Factories');
        }
    }
}
