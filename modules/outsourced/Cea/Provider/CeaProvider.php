<?php

namespace Outsourced\Cea\Provider;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Outsourced\Cea\ConsultaSerialConnection\CeaSerialHeaders;
use Outsourced\Cea\ConsultaSerialConnection\CeaSerialSoapClient;
use Outsourced\Cea\GiftCardConnection\CeaHeaders;
use Outsourced\Cea\GiftCardConnection\CeaSoapClient;
use Outsourced\Cea\tests\ServerTest\CeaGiftCardServerMock;
use Outsourced\Cea\tests\ServerTest\CeaSerialServerMock;
use SoapClient;
use TradeAppOne\Domain\Enumerators\Environments;

class CeaProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ .  '/../Translations', 'cea');
        $this->loadFactoriesFrom(__DIR__ . '/../tests/Factories');
        $this->loadRoutes(__DIR__ . '/../Routes/ceaApi.php');
    }

    public function register()
    {
        switch (App::environment()) {
            case Environments::PRODUCTION:
                $this->clientsProduction();
                break;

            case Environments::BETA:
                $this->clientsHomologation();
                break;

            default:
                $this->clientsTest();
        }
    }

    private function loadRoutes(string $path): void
    {
        Route::prefix('outsourced/cea')
            ->middleware('api')
            ->group($path);
    }

    protected function loadFactoriesFrom(string $path): void
    {
        if (Environments::PRODUCTION !== App::environment()) {
            $this->app->make(Factory::class)->load($path);
        }
    }

    private function clientsTest(): void
    {
        $this->app->singleton(CeaSoapClient::class, static function () {
            $ceaGiftCard = CeaGiftCardServerMock::get();
            return new CeaSoapClient($ceaGiftCard);
        });

        $this->app->singleton(CeaSerialSoapClient::class, static function () {
            $ceaGiftCard = CeaSerialServerMock::get();
            return new CeaSerialSoapClient($ceaGiftCard);
        });
    }

    private function clientsProduction(): void
    {
        $this->app->singleton(CeaSoapClient::class, static function () {
            $options = [
                'location'     => CeaHeaders::uri(),
                'login'        => CeaHeaders::login(),
                'password'     => CeaHeaders::password(),
                'soap_version' => SOAP_1_2,
                'connection_timeout' => 30
            ];

            $ceaGiftCard = new SoapClient(CeaHeaders::uri(), $options);
            return new CeaSoapClient($ceaGiftCard);
        });

        $this->app->singleton(CeaSerialSoapClient::class, static function () {
            $options = [
                'location'     => CeaSerialHeaders::uri(),
                'soap_version' => SOAP_1_2,
                'connection_timeout' => 30
            ];

            $ceaSerial = new SoapClient(CeaHeaders::uri(), $options);
            return new CeaSerialSoapClient($ceaSerial);
        });
    }

    private function clientsHomologation(): void
    {
        $this->app->singleton(CeaSoapClient::class, static function () {

            $options = [
                'location' => CeaHeaders::uri(),
                'soap_version' => SOAP_1_2
            ];

            $ceaGiftCard = new SoapClient(CeaHeaders::uri(), $options);
            return new CeaSoapClient($ceaGiftCard);
        });

        $this->app->singleton(CeaSerialSoapClient::class, static function () {
            $options = [
                'location'     => CeaSerialHeaders::uri(),
                'soap_version' => SOAP_1_2,
            ];

            $ceaSerial = new SoapClient(CeaSerialHeaders::uri(), $options);
            return new CeaSerialSoapClient($ceaSerial);
        });
    }
}
