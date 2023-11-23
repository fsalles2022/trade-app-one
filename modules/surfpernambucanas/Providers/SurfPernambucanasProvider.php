<?php

declare(strict_types=1);

namespace SurfPernambucanas\Providers;

use Illuminate\Support\ServiceProvider;
use SurfPernambucanas\Assistances\SurfPernambucanasPreAssistance;
use SurfPernambucanas\Services\SurfPernambucanasSaleAssistance;
use TradeAppOne\Domain\Enumerators\Operations;

class SurfPernambucanasProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/surfApi.php');
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'surfpernambucanas');
    }

    public function register(): void
    {
        $this->app->register(PagtelServiceProvider::class);
        $this->app->bind(Operations::SURF_PERNAMBUCANAS, SurfPernambucanasSaleAssistance::class);
        $this->app->bind(Operations::SURF_CORREIOS, SurfPernambucanasSaleAssistance::class);
        $this->app->bind(SurfPernambucanasPreAssistance::class);
    }
}
