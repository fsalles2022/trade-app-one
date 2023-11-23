<?php

namespace Outsourced\Providers;

use Illuminate\Support\ServiceProvider;

class OutsourcedProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations/', 'outsourced');
        $this->loadRoutesFrom(__DIR__ . '/../routes/outsourcedApi.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Cea/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../ViaVarejo/Migrations');
    }
}
