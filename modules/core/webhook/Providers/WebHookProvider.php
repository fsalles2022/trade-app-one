<?php

namespace Core\WebHook\Providers;

use Core\WebHook\Connections\Logs\WebHookLogConfig;
use Core\WebHook\Connections\Logs\WebHookLogConnection;
use Core\WebHook\Observers\WebHookServiceObserver;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticSearch;
use TradeAppOne\Domain\Models\Collections\Service;

class WebHookProvider extends ServiceProvider
{
    public function boot(): void
    {
        Service::observe(WebHookServiceObserver::class);

        $this->mergeConfigFrom(__DIR__ . '/../Config/clients.php', 'webhookClients');
    }

    public function register(): void
    {
        $this->app->singleton(WebHookLogConnection::class, static function () {
            $esConfig = new WebHookLogConfig();
            $esClient = new ElasticSearch($esConfig);
            return new WebHookLogConnection($esClient);
        });
    }
}
