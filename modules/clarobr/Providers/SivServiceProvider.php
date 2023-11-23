<?php

namespace ClaroBR\Providers;

use ClaroBR\Connection\SivConnection;
use ClaroBR\Connection\SivConnectionInterface;
use ClaroBR\Connection\SivHttpClient;
use ClaroBR\Connection\SivRoutes;
use ClaroBR\Console\Commands\ClaroBRSync;
use ClaroBR\Console\Commands\ClaroBRUpdateDependentsCommand;
use ClaroBR\Console\Commands\ClaroBRUpdateDeviceCommand;
use ClaroBR\Console\Commands\ClaroBRUpdateMsisdnCommand;
use ClaroBR\OperationAssistances\ClaroControleBoletoAssistant;
use ClaroBR\OperationAssistances\ClaroControleFacilAssistant;
use ClaroBR\OperationAssistances\ClaroControleFacilV3Assistant;
use ClaroBR\OperationAssistances\ClaroPosAssistance;
use ClaroBR\Services\SivSaleAssistance;
use ClaroBR\SivHeaders;
use ClaroBR\Tests\ServerTest\SivServerMocked;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use Tradehub\Services\TradeHubService;

class SivServiceProvider extends ServiceProvider
{
    protected $commands = [
        ClaroBRSync::class,
        ClaroBRUpdateDependentsCommand::class,
        ClaroBRUpdateDeviceCommand::class,
        ClaroBRUpdateMsisdnCommand::class
    ];

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../translations', 'siv');
        $this->loadRoutesFrom(__DIR__ . '/../routes/sivApi.php');
        $this->commands($this->commands);
    }

    public function register()
    {
        $this->app->singleton(
            SivHeaders::class,
            static function () {
                return new SivHeaders(config('integrations.siv'));
            }
        );

        $this->app->bind(
            SivHttpClient::class,
            function () {
                $siv    = $this->app->make(SivHeaders::class);
                $client = new Client(
                    [
                        'base_uri' => $siv->getUri(),
                        'headers' => $siv->getHeaders(),
                        'connect_timeout' => 15.14,
                        'verify' => false
                    ]
                );
                return new SivHttpClient($client);
            }
        );

        $this->app->bind(
            SivConnectionInterface::class,
            function () {
                $properties         = $this->app->make(SivRoutes::class);
                $connectionResolver = $this->app->make(SivHttpClient::class);
                return new SivConnection($properties, $connectionResolver);
            }
        );

        $this->app->bind(
            SivSaleAssistance::class,
            static function () {
                return new SivSaleAssistance(
                    app()->make(SivConnectionInterface::class),
                    app()->make(SaleRepository::class)
                );
            }
        );

        $this->app->bind(
            ClaroControleBoletoAssistant::class,
            static function () {
                return new ClaroControleBoletoAssistant(
                    app()->make(SivConnectionInterface::class),
                    app()->make(SaleRepository::class)
                );
            }
        );

        $this->app->bind(
            ClaroControleFacilAssistant::class,
            static function () {
                return new ClaroControleFacilAssistant(
                    app()->make(SivConnectionInterface::class),
                    app()->make(SaleRepository::class)
                );
            }
        );

        $this->app->bind(
            ClaroControleFacilV3Assistant::class,
            static function () {
                return new ClaroControleFacilV3Assistant(
                    app()->make(SivConnectionInterface::class),
                    app()->make(SaleRepository::class),
                    app()->make(TradeHubService::class)
                );
            }
        );

        $this->app->bind(
            ClaroPosAssistance::class,
            static function () {
                return new ClaroPosAssistance(
                    app()->make(SivConnectionInterface::class),
                    app()->make(SaleRepository::class)
                );
            }
        );

        $this->app->bind(Operations::CLARO, SivSaleAssistance::class);

        if (in_array(App::environment(), [Environments::TEST])) {
            $this->mockSivHttpClient();
        }
    }

    private function mockSivHttpClient(): void
    {
        $this->app->bind(
            SivHttpClient::class,
            static function () {
                $mock    = new SivServerMocked();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new SivHttpClient($client);
            }
        );
    }
}
