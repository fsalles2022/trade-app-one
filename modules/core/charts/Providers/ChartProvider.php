<?php


namespace Core\Charts\Providers;

use Core\Charts\Console\Commands\PopulateChartTableCommand;
use Illuminate\Support\ServiceProvider;

class ChartProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->commands(PopulateChartTableCommand::class);
    }
}
