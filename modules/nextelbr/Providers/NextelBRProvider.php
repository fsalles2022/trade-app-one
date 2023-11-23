<?php

namespace NextelBR\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use NextelBR\Assistance\NextelBRSaleAssistance;
use NextelBR\Connection\M4uModal\Headers\NextelBRTradeUpHeader;
use NextelBR\Connection\M4uModal\NextelBRModalHttpClient;
use NextelBR\Connection\NextelBR\Headers\NextelBRHeaders;
use NextelBR\Connection\NextelBR\NextelBRHttpClient;
use NextelBR\Tests\ServerTest\NextelBRModalServerMock;
use NextelBR\Tests\ServerTest\NextelBRServeMock;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;

class NextelBRProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'nextelBR');
        $this->loadRoutesFrom(__DIR__ . '/../routes/nextelRoutes.php');
    }

    public function register()
    {
        $this->app->bind(NextelBRHttpClient::class, function () {
            $client = new Client(['base_uri' => NextelBRHeaders::uri(), 'headers' => NextelBRHeaders::headers()]);
            return new NextelBRHttpClient($client);
        });

        $this->app->singleton(NextelBRModalHttpClient::class, function () {
            $client = new Client([
                'base_uri' => NextelBRTradeUpHeader::uri(),
                'headers'  => NextelBRTradeUpHeader::headers()
            ]);
            return new NextelBRModalHttpClient($client);
        });

        $this->app->singleton(Operations::NEXTEL, function () {
            return resolve(NextelBRSaleAssistance::class);
        });

        if (App::environment() == Environments::TEST) {
            $this->app->bind(NextelBRHttpClient::class, function () {
                $mock    = new NextelBRServeMock();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new NextelBRHttpClient($client);
            });
            $this->app->bind(NextelBRModalHttpClient::class, function () {
                $mock    = new NextelBRModalServerMock();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new NextelBRModalHttpClient($client);
            });
        }
    }
}
