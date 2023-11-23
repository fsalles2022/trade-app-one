<?php

declare(strict_types=1);

namespace SalesSimulator\Claro\Residential\Providers;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Connection\SivConnectionInterface;
use Illuminate\Support\ServiceProvider;

class SalesSimulatorResidentialServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/simulatorClaroResidentialApi.php');
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'simulatorClaroResidential');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
    }

    public function register(): void
    {
        $this->app->bind(SivConnectionInterface::class, SivConnection::class);
    }
}
