<?php

namespace Outsourced\ViaVarejo\Provider;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Outsourced\ViaVarejo\Connections\Headers\ViaVarejoHeaders;
use Outsourced\ViaVarejo\Connections\ViaVarejoHttpClient;
use Outsourced\ViaVarejo\tests\ServerTest\ViaVarejoServerMock;
use TradeAppOne\Domain\Enumerators\Environments;

class ViaVarejoProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'via_varejo');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/viaVarejoApi.php');
        $this->loadMigrationsFrom(__DIR__ . '/../ViaVarejo/Migrations');
        $this->registerFactories();
    }

    public function register(): void
    {
        $this->app->bind(ViaVarejoHttpClient::class, static function () {
            $headers = app()->make(ViaVarejoHeaders::class);
            $client  = new Client([
                'base_uri' => $headers->getUri(),
                'verify' => false,
                'connect_timeout' => 15.14,
            ]);
            return new ViaVarejoHttpClient($client);
        });

        if (App::environment() === Environments::TEST) {
            $this->app->bind(ViaVarejoHttpClient::class, static function () {
                $mock    = new ViaVarejoServerMock();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new ViaVarejoHttpClient($client);
            });
        }
    }

    private function registerFactories(): void
    {
        if (App::environment() !== Environments::PRODUCTION) {
            app(Factory::class)->load(__DIR__ . '/../tests/Factories');
        }
    }
}
