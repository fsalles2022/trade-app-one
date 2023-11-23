<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use SurfPernambucanas\Connection\PagtelHttpClient;
use SurfPernambucanas\Tests\ServerTest\PagtelServerMocked;

trait BindPagtelHttpClientMock
{
    protected function bindPagtelHttpClient(): void
    {
        $this->app->bind(PagtelHttpClient::class, function (): PagtelHttpClient {
            $mock    = new PagtelServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);

            return new PagtelHttpClient($client);
        });
    }
}
