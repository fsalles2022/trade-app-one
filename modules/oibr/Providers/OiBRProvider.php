<?php

namespace OiBR\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use OiBR\Assistance\OiBRSaleAssistance;
use OiBR\Connection\ElDoradoGateway\ElDoradoHeaders;
use OiBR\Connection\ElDoradoGateway\ElDoradoHttpClient;
use OiBR\Connection\OiBRHeaders;
use OiBR\Connection\OiBRHttpClient;
use OiBR\Tests\ServerTest\OiBRServeMock;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;

class OiBRProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'oiBR');
        $this->loadRoutesFrom(__DIR__ . '/../routes/oiBRApi.php');
    }

    public function register()
    {
        $this->app->bind(OiBRHeaders::class, function () {
            return new OiBRHeaders(config('integrations.oiBR'));
        });

        $this->app->bind(ElDoradoHeaders::class, function () {
            return new ElDoradoHeaders(config('integrations.oiBR.eldorado'));
        });
        $this->app->bind(OiBRHttpClient::class, function () {
            if (App::environment() == Environments::TEST) {
                $mock    = new OiBRServeMock();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new OiBRHttpClient($client);
            } else {
                $oiBR = $this->app->make(OiBRHeaders::class);
                return new OiBRHttpClient(new Client([
                    'base_uri'        => $oiBR->getUri(),
                    'headers'         => $oiBR->getHeaders(),
                    'connect_timeout' => 15.14
                ]));
            }
        });
        $this->app->bind(ElDoradoHttpClient::class, function () {
            $oiBR = $this->app->make(ElDoradoHeaders::class);
            return new ElDoradoHttpClient(new Client([
                'base_uri'        => $oiBR->getUri(),
                'headers'         => $oiBR->getHeaders(),
                'connect_timeout' => 15.14
            ]));
        });

        $this->app->bind(Operations::OI, function () {
            return resolve(OiBRSaleAssistance::class);
        });
    }
}
