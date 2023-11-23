<?php

namespace Movile\Providers;

use GuzzleHttp\Client;
use Movile\Assistance\MovileSaleAssistance;
use Movile\Connection\MovileHttpClient;
use Movile\Movile;
use TradeAppOne\Domain\Enumerators\Operations;
use Illuminate\Support\ServiceProvider;

class MovileProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'movile');
    }

    public function register()
    {
        $this->app->bind(MovileHttpClient::class, function () {
            $client = new Client([
                'base_uri' => Movile::uri()
            ]);

            return new MovileHttpClient($client);
        });
        $this->app->bind(Operations::MOVILE, MovileSaleAssistance::class);
    }
}
