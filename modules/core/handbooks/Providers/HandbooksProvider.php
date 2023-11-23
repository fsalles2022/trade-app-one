<?php

namespace Core\HandBooks\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;

class HandbooksProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/handbookApi.php');
        $this->loadTranslationsFrom(__DIR__ . '/../Translations', 'handbook');
        $this->loadFactoriesFrom(__DIR__ . '/../Database/Factories');
    }

    protected function loadFactoriesFrom(string $path): void
    {
        if (Environments::PRODUCTION !== App::environment()) {
            $this->app->make(Factory::class)->load($path);
        }
    }
}
