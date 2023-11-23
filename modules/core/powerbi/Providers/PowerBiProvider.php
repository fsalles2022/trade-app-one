<?php

namespace Core\PowerBi\Providers;

use Core\PowerBi\Connections\PowerBiClient;
use Core\PowerBi\Http\Middleware\CheckPowerBiAvailabilityMiddleware;
use Core\PowerBi\tests\Server\PowerBiServerMock;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;

class PowerBiProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerConfig();
        $this->registerTranslations();
        $this->registerRoutes();
        $this->registerClient();
        $this->registerMiddleware();
    }

    public function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('checkPowerBiAvailability', CheckPowerBiAvailabilityMiddleware::class);
    }

    public function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ .'/../Translations', 'pbi');
    }

    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/pbi_config.php', 'pbi');
    }

    private function registerRoutes(): void
    {
        Route::prefix('pbi')
            ->middleware('api')
            ->group(__DIR__. '/../Routes/pbiApi.php');
    }

    private function registerClient(): void
    {
        if (App::environment() === Environments::TEST) {
            $this->app->bind(PowerBiClient::class, static function () {
                $mock    = new PowerBiServerMock();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new PowerBiClient($client);
            });
        }
    }
}
