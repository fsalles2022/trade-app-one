<?php

declare(strict_types=1);

namespace Tradehub\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;
use Tradehub\Connection\TradeHubConnection;
use Tradehub\Connection\TradeHubHttpClient;
use Tradehub\Tests\ServerTest\TradeHubServerMocked;
use Tradehub\TradeHubHeaders;

class TradehubServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'tradehub');
        $this->loadRoutesFrom(__DIR__  . '/../routes/tradehubApi.php');
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(TradeHubHeaders::class, function (): TradeHubHeaders {
            return new TradeHubHeaders(config('integrations.tradehub'));
        });

        $this->app->bind(
            TradeHubHttpClient::class,
            function () {
                $tradehub = $this->app->make(TradeHubHeaders::class);
                $client   = new Client(
                    [
                        'base_uri' => $tradehub->getUri(),
                        'headers' => $tradehub->getHeaders(),
                    ]
                );
                return new TradeHubHttpClient($client);
            }
        );

        $this->app->bind(TradeHubConnection::class);

        if (App::environment() === Environments::TEST) {
            $this->mockTradeHubHttpClient();
        }
    }

    private function mockTradeHubHttpClient(): void
    {
        $this->app->bind(TradeHubHttpClient::class, static function () {
            $mock    = new TradeHubServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new TradeHubHttpClient($client);
        });
    }
}
