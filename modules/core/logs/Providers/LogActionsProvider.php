<?php

namespace Core\Logs\Providers;

use Core\Logs\Connection\LogActionsConfig;
use Core\Logs\Connection\LogActionsConnection;
use Core\Logs\Observers\LogActionsObserver;
use Discount\Models\Discount;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticSearch;

class LogActionsProvider extends ServiceProvider
{
    public function boot()
    {
        Discount::observe(LogActionsObserver::class);
    }

    public function register()
    {
        $this->app->singleton(LogActionsConnection::class, static function () {
            $esConfig = new LogActionsConfig();
            $esClient = new ElasticSearch($esConfig);

            return new LogActionsConnection($esClient);
        });
    }
}
