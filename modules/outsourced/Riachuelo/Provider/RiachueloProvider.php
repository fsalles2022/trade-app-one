<?php

namespace Outsourced\Riachuelo\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Outsourced\Riachuelo\Connections\Headers\RiachueloHeaders;
use Outsourced\Riachuelo\Connections\RiachueloHttpClient;
use Outsourced\Riachuelo\tests\ServerTest\RiachueloServeMock;
use TradeAppOne\Domain\Enumerators\Environments;

class RiachueloProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'riachuelo');
    }

    public function register()
    {
        $this->app->bind(RiachueloHttpClient::class, static function () {
            $headers = app()->make(RiachueloHeaders::class);
            $client  = new Client([
                'base_uri' => $headers->getUri(),
                'verify' => false,
                'connect_timeout' => 15.14,
            ]);
            return new RiachueloHttpClient($client);
        });

        if (App::environment() === Environments::TEST) {
            $this->app->bind(RiachueloHttpClient::class, static function () {
                $mock    = new RiachueloServeMock;
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new RiachueloHttpClient($client);
            });
        }
    }
}
