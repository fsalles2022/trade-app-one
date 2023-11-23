<?php

namespace Bulletin\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;

class BulletinProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerFactories();
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'bulletin');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    private function registerFactories(): void
    {
        if (App::environment() !== Environments::PRODUCTION) {
            app(Factory::class)->load(__DIR__ . '/../tests/Factories');
        }
    }
}
