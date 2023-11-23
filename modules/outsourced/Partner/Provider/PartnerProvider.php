<?php

namespace Outsourced\Partner\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\ServiceProvider;
use Outsourced\Partner\Connections\PartnerHttpClient;
use Outsourced\Partner\Connections\ViaVarejoHeaders;
use Outsourced\Partner\tests\ServerTest\PartnerServerMock;
use TradeAppOne\Domain\Enumerators\Environments;

class PartnerProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'partner');
        $this->loadRoutesFrom(__DIR__ . '/../routes/partnerApi.php');
    }

    public function register(): void
    {
        $this->app->bind(PartnerHttpClient::class, static function () {
            $client = new Client([
                'verify' => false
            ]);
            return new PartnerHttpClient($client);
        });

        $this->app->singleton(ViaVarejoHeaders::class, static function () {
            return new ViaVarejoHeaders(config('integrations.viaVarejo'));
        });

        if ($this->app->environment() === Environments::TEST) {
            $this->mockPartnerHttpClient();
        }
    }

    private function mockPartnerHttpClient(): void
    {
        $this->app->bind(PartnerHttpClient::class, static function () {
            $mock    = new PartnerServerMock();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new PartnerHttpClient($client);
        });
    }
}
