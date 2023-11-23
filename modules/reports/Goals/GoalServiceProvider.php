<?php

namespace Reports\Goals;

use Illuminate\Support\ServiceProvider;

class GoalServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadTranslationsFrom(__DIR__ . '/translations/', 'goals');
        (resolve(GoalPolicy::class))->registerPolicies();
    }
}
