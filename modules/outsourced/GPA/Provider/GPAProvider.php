<?php


namespace Outsourced\GPA\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Outsourced\Enums\Outsourced;
use Outsourced\GPA\Commands\GPASentinel;
use Outsourced\GPA\Connections\GPAHttpClient;
use Outsourced\GPA\Connections\GPARoutes;
use Outsourced\GPA\Connections\Headers\GPAHeaders;
use Outsourced\GPA\tests\ServerMock\GPAServerMock;
use TradeAppOne\Domain\Enumerators\Environments;

class GPAProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands(GPASentinel::class);
        $this->loadTranslationsFrom(__DIR__ . '/../translations', Outsourced::GPA);
    }

    public function register(): void
    {
        (App::environment() === Environments::TEST)
            ? $this->testClient()
            : $this->productionClient();
    }

    public function testClient(): void
    {
        $this->app->bind(GPAHttpClient::class, static function () {
            $mock    = new GPAServerMock;
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new GPAHttpClient($client);
        });
    }

    private function productionClient(): void
    {
        $this->app->bind(GPAHttpClient::class, static function () {
            $accessToken = self::getAccessToken();

            $client = new Client([
                'verify' => false,
                'base_uri' => GPAHeaders::getUri(),
                'headers' => [
                    'Authorization' => 'Bearer' . $accessToken,
                    'X-Api-Key' => GPAHeaders::xApiKey()
                ],
            ]);

            return new GPAHttpClient($client);
        });
    }

    private static function getAccessToken()
    {
        $client = new Client([
            'verify' => false,
            'base_uri' => GPAHeaders::getUri(),
            'auth' => [GPAHeaders::username(), GPAHeaders::password()],
            'headers' => [
                'X-Api-Key' => GPAHeaders::xApiKey()
            ]
        ]);

        $response = (new GPAHttpClient($client))->postFormParams(GPARoutes::AUTH, [
            'username' => GPAHeaders::username(),
            'password' => GPAHeaders::password(),
            'grant_type' =>  GPAHeaders::grantType()
        ])->toArray();

        return Arr::get($response, 'access_token', '');
    }
}
