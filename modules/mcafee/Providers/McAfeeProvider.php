<?php

namespace McAfee\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use McAfee\Connection\McAfeeConnection;
use McAfee\Connection\McAfeeConnectionInterface;
use McAfee\Connection\McAfeeHeaders;
use McAfee\Connection\McAfeeSoapClient;
use McAfee\Console\McAfeeTrialCommand;
use McAfee\Services\McAfeeSaleAssistance;
use McAfee\Tests\ServerTest\McAfeeServerMock;
use Mockery;
use SoapClient;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;

class McAfeeProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'mcAfee');
        $this->loadRoutesFrom(__DIR__ . '/../routes/mcafeeApi.php');
        $this->commands(McAfeeTrialCommand::class);
        $this->loadFactories();
    }

    public function register()
    {
        $this->app->singleton(McAfeeConnectionInterface::class, McAfeeConnection::class);

        if (Environments::TEST === App::environment() || Environments::BETA === App::environment()) {
            $this->app->singleton(McAfeeSoapClient::class, function () {
                $client = Mockery::mock(McAfeeSoapClient::class)->makePartial();
                $client->shouldReceive('execute')->withAnyArgs()->andReturn(new McAfeeServerMock());
                return $client;
            });
        } else {
            $this->app->singleton(McAfeeSoapClient::class, function () {
                $client = new SoapClient(McAfeeHeaders::uri());
                $client->__setLocation(McAfeeHeaders::uri());
                return new McAfeeSoapClient($client);
            });
        }

        $this->app->singleton(Operations::MCAFEE, McAfeeSaleAssistance::class);
    }

    private function loadFactories()
    {
        if (App::environment() !== Environments::PRODUCTION) {
            app(Factory::class)->load(__DIR__ . '/../Factories');
        }
    }
}
