<?php

namespace FastShop\Providers;

use FastShop\Connection\FastshopConnection;
use FastShop\Connection\FastshopConnectionInterface;
use FastShop\Connection\FastshopHeaders;
use FastShop\Connection\FastshopHttpClient;
use FastShop\Console\Commands\ProductPlansSync;
use FastShop\tests\ServerTest\FastshopServerMocked;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;

class FastShopProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'fastshop');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/fastshopApi.php');
        $this->commands([ProductPlansSync::class]);
    }

    public function register(): void
    {
        $this->app->singleton(FastshopHeaders::class, static function () {
            return new FastshopHeaders(config('integrations.fastshop'));
        });

        $this->app->bind(FastshopHttpClient::class, function () {
            $fastShop = $this->app->make(FastshopHeaders::class);
            $client   = new Client([
                'base_uri'        => $fastShop->getUri(),
                'headers'         => $fastShop->getHeaders(),
                'connect_timeout' => $fastShop->getTimeoutConnection(),
                'verify'          => $fastShop->getVerifyConnection()
            ]);
            return new FastshopHttpClient($client);
        });

        if ($this->app->environment() === Environments::TEST) {
            $this->mockFastshopHttpClient();
        }

        $this->app->bind(FastshopConnectionInterface::class, function () {
            $connectionResolver = $this->app->make(FastshopHttpClient::class);
            return new FastshopConnection($connectionResolver);
        });
    }

    private function mockFastshopHttpClient(): void
    {
        $this->app->bind(FastshopHttpClient::class, static function () {
            $mock    = new FastshopServerMocked;
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new FastshopHttpClient($client);
        });
    }
}
