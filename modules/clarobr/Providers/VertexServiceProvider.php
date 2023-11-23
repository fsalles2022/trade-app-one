<?php


namespace ClaroBR\Providers;

use ClaroBR\Connection\VertexConnection;
use ClaroBR\Connection\VertexConnectionInterface;
use ClaroBR\Connection\VertexHeaders;
use ClaroBR\Connection\VertexHttpClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class VertexServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'siv');
    }

    public function register()
    {
        $this->app->singleton(VertexHeaders::class, static function () {
            return new VertexHeaders(config('integrations.vertex'));
        });

        $this->app->bind(VertexHttpClient::class, function () {
            $headers = $this->app->make(VertexHeaders::class);
            $client  = new Client([
                'base_uri' => $headers->getUri(),
                'headers' => $headers->getHeaders(),
                'connect_timeout' => 15.14,
                'verify' => false
            ]);
            return new VertexHttpClient($client);
        });

        $this->app->bind(VertexConnectionInterface::class, function () {
            $connection = $this->app->make(VertexHttpClient::class);
            return new VertexConnection($connection);
        });
    }
}
