<?php

namespace GA\Providers;

use GA\Connections\GAClient;
use GA\Connections\Headers\GAHeaders;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class GAProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerConfig();
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'activations');
    }

    public function register(): void
    {
        $this->app->singleton(GAClient::class, static function () {
            $client   = new Client(['base_uri' => GAHeaders::getUri()]);
            $gaClient = new GAClient($client);
            $gaClient->pushHeader([
                'client'    => GAHeaders::getClient(),
                'x-api-key' => GAHeaders::getApiKey()
            ]);

            return $gaClient;
        });
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/gateway_activations.php', 'activations');
    }
}
