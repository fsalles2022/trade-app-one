<?php

namespace Uol\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use SoapClient;
use SoapHeader;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;
use Uol\Assistance\UolSaleAssistance;
use Uol\Connection\Passaporte\UolPassaporteSoapClient;
use Uol\Connection\UolHeaders;
use Uol\Enumerators\UolWebServicesEnum;
use Uol\Tests\ServerTest\UolPassaporteServerMock;

class UolProvider extends ServiceProvider
{
    const WSDL              = '?wsdl';
    const DEFAULT_NAMESPACE = 'http://tempuri.org/';
    const DEFAULT_NAME      = 'AutenticacaoServico';

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'uol');
        $this->loadRoutesFrom(__DIR__ . '/../routes/UolApi.php');
        $this->loadViewsFrom(__DIR__ . '/../views', 'uol');
    }

    public function register()
    {
        if (Environments::TEST == App::environment()) {
            $passaporteClient = (new UolPassaporteServerMock())->getSoapClient();
            $this->app->instance(UolPassaporteSoapClient::class, new UolPassaporteSoapClient($passaporteClient));
        } else {
            $credentials = array(
                'email' => UolHeaders::getMail(),
                'senha' => UolHeaders::getPassword()
            );

            $header = new SoapHeader(self::DEFAULT_NAMESPACE, self::DEFAULT_NAME, $credentials);

            $this->app->singleton(UolPassaporteSoapClient::class, function () use ($header) {
                $passaporteClient = new SoapClient(UolHeaders::uri() . UolWebServicesEnum::PASSAPORTE . self::WSDL);
                $passaporteClient->__setSoapHeaders(array($header));
                return new UolPassaporteSoapClient($passaporteClient);
            });
        }
        $this->app->singleton(Operations::UOL, UolSaleAssistance::class);
    }
}
