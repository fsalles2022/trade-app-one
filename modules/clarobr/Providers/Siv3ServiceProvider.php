<?php

declare(strict_types=1);

namespace ClaroBR\Providers;

use ClaroBR\Connection\Siv3Connection;
use ClaroBR\Connection\Siv3HttpClient;
use ClaroBR\Siv3Headers;
use ClaroBR\Tests\Siv3Tests\ServerTest\Siv3ServerMocked;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Enumerators\Environments;

class Siv3ServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/siv3Api.php');
    }

    public function register(): void
    {
        $this->app->bind(Siv3Headers::class, function (): Siv3Headers {
            return new Siv3Headers(config('integrations.siv3'));
        });

        $this->app->bind(
            Siv3HttpClient::class,
            function () {
                $siv    = $this->app->make(Siv3Headers::class);
                $client = new Client(
                    [
                        'base_uri' => $siv->getUri(),
                        'headers' => $siv->getHeaders(),
                    ]
                );
                return new Siv3HttpClient($client);
            }
        );

        $this->app->bind(Siv3Connection::class);

        if (App::environment() === Environments::TEST) {
            $this->mockSiv3HttpClient();
        }
    }

    private function mockSiv3HttpClient(): void
    {
        $this->app->bind(Siv3HttpClient::class, static function () {
                $mock    = new Siv3ServerMocked();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new Siv3HttpClient($client);
        });
    }
}
