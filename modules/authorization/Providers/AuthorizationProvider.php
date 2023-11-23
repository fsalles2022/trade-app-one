<?php

namespace Authorization\Providers;

use Illuminate\Support\ServiceProvider;

class AuthorizationProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'authorization');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
    }
}
