<?php

namespace Gateway\Providers;

use Gateway\API\Credential;
use Gateway\API\Environment;
use Gateway\API\Gateway;
use Gateway\Connection\GatewayClient;
use Gateway\Connection\GatewayHeaders;
use Gateway\tests\ServerTest\GatewayServerMock;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;

class GatewayProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'gateway');
    }

    public function register()
    {
        switch (App::environment()) {
            case Environments::BETA:
                $environment = Environment::SANDBOX;
                $credential  = new Credential(GatewayHeaders::getId(), GatewayHeaders::getAcessKey(), $environment);
                $gateway     = new Gateway($credential);
                $this->instanceGateway($gateway);
                break;

            case Environments::PRODUCTION:
                $environment = Environment::PRODUCTION;
                $credential  = new Credential(GatewayHeaders::getId(), GatewayHeaders::getAcessKey(), $environment);
                $gateway     = new Gateway($credential);
                $this->instanceGateway($gateway);
                break;

            default:
                $gateway = (new GatewayServerMock())->getGateway();
                $this->instanceGateway($gateway);
                break;
        }
    }

    public function instanceGateway(Gateway $gateway)
    {
        $this->app->instance(GatewayClient::class, new GatewayClient($gateway));
    }
}
