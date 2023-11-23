<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Outsourced\CasaEVideo\Connection\CasaEVideoHeaders;
use Outsourced\CasaEVideo\Connection\CasaEVideoHttpClient;
use Outsourced\CasaEVideo\tests\ServerMock\CasaEVideoServerMock;
use Outsourced\Enums\Outsourced;
use TradeAppOne\Domain\Enumerators\Environments;

class CasaEVideoProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', Outsourced::CASAEVIDEO);
    }

    public function register(): void
    {
        $this->bindHttpClientCasaEVideo();

        if (App::environment() === Environments::TEST) {
            $this->bindHttpClientCasaEVideoMocked();
        }
    }

    private function bindHttpClientCasaEVideo(): void
    {
        $this->app->bind(CasaEVideoHttpClient::class, function (): CasaEVideoHttpClient {
            return new CasaEVideoHttpClient(
                new Client(
                    ['base_uri' => CasaEVideoHeaders::getUri()]
                )
            );
        });
    }

    private function bindHttpClientCasaEVideoMocked(): void
    {
        $this->app->bind(CasaEVideoHttpClient::class, function (): CasaEVideoHttpClient {
            $mock    = new CasaEVideoServerMock();
            $handler = HandlerStack::create($mock);
            return new CasaEVideoHttpClient(new Client(['handler' => $handler]));
        });
    }
}
