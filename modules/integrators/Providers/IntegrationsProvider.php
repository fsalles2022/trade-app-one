<?php

namespace Integrators\Providers;

use Illuminate\Support\ServiceProvider;

class IntegrationsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/integrations.php');
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'integrations');
    }
}
