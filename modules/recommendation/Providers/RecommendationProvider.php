<?php

namespace Recommendation\Providers;

use Illuminate\Support\ServiceProvider;

class RecommendationProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'recommendation');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/recommendationApi.php');
    }
}
