<?php

declare(strict_types=1);

namespace SurfPernambucanas\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use SurfPernambucanas\Connection\PagtelAuthUser;
use SurfPernambucanas\Connection\PagtelConnection;
use SurfPernambucanas\Connection\PagtelHeaders;
use SurfPernambucanas\Connection\PagtelHttpClient;
use SurfPernambucanas\Services\MountNewAttributeFromSurf;
use SurfPernambucanas\Services\MountNewAttributeFromSurfCorreios;
use SurfPernambucanas\Services\PagtelService;
use SurfPernambucanas\Tests\ServerTest\PagtelServerMocked;
use TradeAppOne\Domain\Enumerators\Environments;

class PagtelServiceProvider extends ServiceProvider
{
    /** @inheritDoc */
    protected $defer = true;

    public function register(): void
    {
        // Perbnambucanas Credentials
        $this->app->bind('pagtel.pernambucanas.headers', function (): PagtelHeaders {
            return new PagtelHeaders(config('integrations.pagtel.pernambucanas'));
        });

        $this->app->bind('pagtel.pernambucanas.authUser', function (): PagtelAuthUser {
            return new PagtelAuthUser(config('integrations.pagtel.pernambucanas'));
        });

        $this->app->bind(MountNewAttributeFromSurf::class, function (): MountNewAttributeFromSurf {
            return new MountNewAttributeFromSurf(
                $this->app->make(PagtelService::class, [
                    'client' => PagtelConnection::PAGTEL_PERNAMBUCANAS
                ])
            );
        });

        // Correrios Credentials
        $this->app->bind('pagtel.correios.headers', function (): PagtelHeaders {
            return new PagtelHeaders(config('integrations.pagtel.correios'));
        });

        $this->app->bind('pagtel.correios.authUser', function (): PagtelAuthUser {
            return new PagtelAuthUser(config('integrations.pagtel.correios'));
        });

        $this->app->bind(MountNewAttributeFromSurfCorreios::class, function (): MountNewAttributeFromSurfCorreios {
            return new MountNewAttributeFromSurfCorreios(
                $this->app->make(PagtelService::class, [
                    'client' => PagtelConnection::PAGTEL_CORREIOS
                ])
            );
        });


        $this->app->bind(PagtelHttpClient::class, function ($app, $arguments): PagtelHttpClient {
            $headers = $this->getHeadersByClient(data_get($arguments, 'client'));

            $client = new Client([
                'base_uri'        => $headers->getUri(),
                'headers'         => $headers->getHeaders(),
                'connect_timeout' => $headers->getTimeoutConnection(),
            ]);

            return new PagtelHttpClient($client);
        });

        $this->app->bind(PagtelConnection::class, function ($app, $arguments): PagtelConnection {
            return new PagtelConnection(
                $this->app->make(PagtelHttpClient::class, $arguments),
                $this->getAuthUserByClient(data_get($arguments, 'client'))
            );
        });

        $this->app->bind(PagtelService::class, function ($app, $arguments): PagtelService {
            return new PagtelService(
                $this->app->make(PagtelConnection::class, $arguments)
            );
        });
        
        if (App::environment() === Environments::TEST) {
            $this->mockPagtelServer();
        }
    }

    private function getHeadersByClient(?String $client): PagtelHeaders
    {
        if ($client === PagtelConnection::PAGTEL_CORREIOS) {
            return $this->app->make('pagtel.correios.headers');
        }

        return $this->app->make('pagtel.pernambucanas.headers');
    }

    private function getAuthUserByClient(?String $client): PagtelAuthUser
    {
        if ($client === PagtelConnection::PAGTEL_CORREIOS) {
            return $this->app->make('pagtel.correios.authUser');
        }

        return $this->app->make('pagtel.pernambucanas.authUser');
    }

    private function mockPagtelServer(): void
    {
        $this->app->bind(PagtelHttpClient::class, function (): PagtelHttpClient {
            $mock    = new PagtelServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);

            return new PagtelHttpClient($client);
        });
    }

    /**
     * @inheritDoc
     * @return string[]
     */
    public function provides(): array
    {
        return [
            PagtelHeaders::class,
            PagtelAuthUser::class,
            PagtelHttpClient::class,
            PagtelConnection::class,
            'pagtel.pernambucanas.headers',
            'pagtel.pernambucanas.authUser',
            'pagtel.correios.headers',
            'pagtel.correios.authUser',
        ];
    }
}
