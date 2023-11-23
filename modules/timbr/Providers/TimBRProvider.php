<?php

namespace TimBR\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TimBR\Assistance\TimBRSaleAssistance;
use TimBR\Connection\BrScan\BrScanHttpClient;
use TimBR\Connection\TimBR;
use TimBR\Connection\TimBRElDorado\TimBRElDoradoHttpClient;
use TimBR\Connection\TimExpress\TimBRExpressHttpClient;
use TimBR\Connection\TimPremiumCommissioning\TimCommissioningHttpClient;
use TimBR\Tests\ServerTest\TimBRMockProvider;
use TimBR\Tests\ServerTest\TimBrScanServerMocked;
use TimBR\Tests\ServerTest\TimCommissioningServerMocked;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;

class TimBRProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'timBR');
        $this->loadRoutesFrom(__DIR__ . '/../routes/timBRApi.php');
    }

    public function register()
    {
        $this->app->bind(Operations::TIM, function () {
            return resolve(TimBRSaleAssistance::class);
        });

        $this->app->bind(TimBRExpressHttpClient::class, function () {
            $client = new Client(['base_uri' => TimBR::getExpressUri()]);
            return new TimBRExpressHttpClient($client);
        });

        $this->app->bind(TimBRElDoradoHttpClient::class, function () {
            $client = new Client([
                'base_uri' => TimBR::getElDoradoUri()
            ]);
            return new TimBRElDoradoHttpClient($client);
        });

        $this->app->bind(BrScanHttpClient::class, function (): BrScanHttpClient {
            $client = new Client([
                'base_uri' => TimBR::getBrScanUri()
            ]);

            return new BrScanHttpClient($client);
        });

        $this->app->bind(TimCommissioningHttpClient::class, function (): TimCommissioningHttpClient {
            $client = new Client([
                'base_uri' => TimBR::getPremiumCommissioningUri(),
                'verify' => false
            ]);

            return new TimCommissioningHttpClient($client);
        });


        if (Environments::TEST === App::environment()) {
            $timBRMockProvider = new TimBRMockProvider($this->app);
            $timBRMockProvider->register();

            $this->mockTimCommissioningHttpClient();
            $this->mockBrScanHttpClient();
        }
    }

    private function mockBrScanHttpClient(): void
    {
        $this->app->bind(BrScanHttpClient::class, function () {
            $mock    = new TimBrScanServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new BrScanHttpClient($client);
        });
    }

    private function mockTimCommissioningHttpClient(): void
    {
        $this->app->bind(TimCommissioningHttpClient::class, function () {
            $mock    = new TimCommissioningServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new TimCommissioningHttpClient($client);
        });
    }
}
