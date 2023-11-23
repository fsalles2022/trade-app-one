<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Outsourced\Enums\Outsourced;
use Outsourced\Pernambucanas\Connections\Headers\PernambucanasHeaders;
use Outsourced\Pernambucanas\Connections\PernambucanasHttpClient;
use Outsourced\Pernambucanas\tests\ServerMock\PernambucanasServerMock;
use TradeAppOne\Domain\Enumerators\Environments;

class PernambucanasProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', Outsourced::PERNAMBUCANAS);
    }

    public function register(): void
    {
        (App::environment() === Environments::TEST)
            ? $this->testHttpClient()
            : $this->productionHttpClient();
    }

    private function testHttpClient(): void
    {
        $this->app->bind(PernambucanasHttpClient::class, function (): PernambucanasHttpClient {
            $mock    = new PernambucanasServerMock();
            $handler = HandlerStack::create($mock);
            return new PernambucanasHttpClient(new Client(['handler' => $handler]));
        });
    }

    private function productionHttpClient(): void
    {
        $this->app->bind(PernambucanasHttpClient::class, function (): PernambucanasHttpClient {
            return new PernambucanasHttpClient(
                new Client([
                    'verify' => false,
                    'base_uri' => PernambucanasHeaders::getUri(),
                ])
            );
        });
    }
}
