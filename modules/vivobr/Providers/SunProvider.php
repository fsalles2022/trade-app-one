<?php

namespace VivoBR\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Components\RestClient\GuzzleHelper;
use TradeAppOne\Domain\Components\RestClient\Rest;
use TradeAppOne\Domain\Enumerators\Environments;
use TradeAppOne\Domain\Enumerators\Operations;
use VivoBR\BaseSunHeaders;
use VivoBR\Connection\Headers\AvenidaSunHeaders;
use VivoBR\Connection\Headers\CasaEVideoSunHeaders;
use VivoBR\Connection\Headers\CeaSunHeaders;
use VivoBR\Connection\Headers\EletrozemaSunHeaders;
use VivoBR\Connection\Headers\ExtraSunHeaders;
use VivoBR\Connection\Headers\FastShopSunHeaders;
use VivoBR\Connection\Headers\FuijokaSunHeaders;
use VivoBR\Connection\Headers\HervalSunHeaders;
use VivoBR\Connection\Headers\LebesSunHeaders;
use VivoBR\Connection\Headers\PernambucanasSunHeaders;
use VivoBR\Connection\Headers\RiachueloSunHeaders;
use VivoBR\Connection\Headers\SchumannSunHeaders;
use VivoBR\Connection\Headers\SunHeader;
use VivoBR\Connection\SunConnection;
use VivoBR\Connection\SunHttpClient;
use VivoBR\Console\Commands\VivoBRSync;
use VivoBR\Repositories\VivoBRUserRepository;
use VivoBR\Services\UserRegistrationVivoService;
use VivoBR\Services\VivoBrSaleAssistance;
use VivoBR\Tests\ServerTest\SunServerMocked;

class SunProvider extends ServiceProvider
{
    protected $commands = [VivoBRSync::class];

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/sunApi.php');
        $this->loadTranslationsFrom(__DIR__. '/../translations', 'sun');
    }

    public function register(): void
    {
        $this->commands($this->commands);

        app()->bind(Rest::class, static function () {
            return new GuzzleHelper(new Client());
        });

        $this->app->singleton(SunHeader::class, static function () {
            return new BaseSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(CeaSunHeaders::class, static function () {
            return new CeaSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(RiachueloSunHeaders::class, static function () {
            return new RiachueloSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(HervalSunHeaders::class, static function () {
            return new HervalSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(PernambucanasSunHeaders::class, static function () {
            return new PernambucanasSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(LebesSunHeaders::class, static function () {
            return new LebesSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(ExtraSunHeaders::class, static function () {
            return new ExtraSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(SchumannSunHeaders::class, static function () {
            return new SchumannSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(EletrozemaSunHeaders::class, static function () {
            return new EletrozemaSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(FuijokaSunHeaders::class, static function () {
            return new FuijokaSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(CasaEVideoSunHeaders::class, static function () {
            return new CasaEVideoSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(FastShopSunHeaders::class, static function () {
            return new FastShopSunHeaders(config('integrations.sun'));
        });

        $this->app->singleton(AvenidaSunHeaders::class, static function () {
            return new AvenidaSunHeaders(config('integrations.sun'));
        });

        app()->bind(SunHttpClient::class, static function ($app) {
            return new SunHttpClient($app->make(Rest::class), app()->make(SunHeader::class));
        });

        $this->app->bind(SunConnection::class, static function ($app) {
            return new SunConnection(
                $app->make(SunHttpClient::class)
            );
        });

        $this->app->singleton(UserRegistrationVivoService::class, static function () {
            return new UserRegistrationVivoService(resolve(VivoBRUserRepository::class));
        });

        $this->app->bind(Operations::VIVO, VivoBrSaleAssistance::class);

        if (App::environment() === Environments::TEST) {
            $this->mockSun();
        }
    }

    private function mockSun(): void
    {
        $this->app->singleton(Rest::class, static function () {
            $mock    = new SunServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new GuzzleHelper($client);
        });
    }
}
