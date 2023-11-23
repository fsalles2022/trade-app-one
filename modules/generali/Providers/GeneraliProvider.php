<?php

namespace Generali\Providers;

use GA\Connections\GAClient;
use Generali\Assistance\GeneraliSaleAssistance;
use Generali\Connection\GeneraliHttpClient;
use Generali\Console\Commands\GeneraliSentinel;
use Generali\tests\ServerMock\GeneraliServerMock;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;

class GeneraliProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerFactories();
        $this->commands(GeneraliSentinel::class);
        $this->loadRoutesFrom(__DIR__ . '/../routes/generaliApi.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
        $this->loadViewsFrom(__DIR__ . '/../views', 'generaliViews');
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'generali');
    }

    public function register(): void
    {
        if (App::environment() === Environments::TEST) {
            $this->app->bind(GeneraliHttpClient::class, static function () {
                $mock    = new GeneraliServerMock;
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new GeneraliHttpClient($client);
            });

            $this->app->bind(GAClient::class, static function () {
                $mock    = new GeneraliServerMock;
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new GAClient($client);
            });
        }

        $this->app->bind(Operations::GENERALI, GeneraliSaleAssistance::class);
    }

    private function registerFactories(): void
    {
        if (App::environment() !== Environments::PRODUCTION) {
            app(Factory::class)->load(__DIR__ . '/../tests/Factories');
        }
    }
}
