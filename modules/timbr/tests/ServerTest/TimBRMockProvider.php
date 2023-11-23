<?php

namespace TimBR\Tests\ServerTest;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Cache;
use TimBR\Connection\Authentication\AuthenticationConnection;
use TimBR\Connection\Authentication\TimBRUserBearerHttp;
use TimBR\Connection\TimBRElDorado\TimBRElDoradoHttpClient;
use TimBR\Connection\TimBRHttpClient;
use TimBR\Connection\TimExpress\TimBRExpressHttpClient;
use TimBR\Enumerators\TimBRCacheables;
use TimBR\Tests\TimBRTestBook;

class TimBRMockProvider
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function register()
    {
        $this->app->singleton(TimBRHttpClient::class, function () {
            $mock    = new TimServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new TimBRHttpClient($client);
        });

        //FIXME melhorar forma de mockar o express
        $this->app->bind(TimBRElDoradoHttpClient::class, function () {
            $mock    = new ElDoradoServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new TimBRElDoradoHttpClient($client);
        });

        $this->app->singleton(TimBRExpressHttpClient::class, function () {
            $mock    = new TimExpressServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new TimBRExpressHttpClient($client);
        });

        $mock         = new TimServerMocked();
        $handler      = HandlerStack::create($mock);
        $client       = new Client(['handler' => $handler]);
        $clientMocked = new TimBRHttpClient($client);
        $bearer       = TimBRTestBook::getCeaBearer();

        $this->app->singleton(AuthenticationConnection::class, function () use ($clientMocked) {
            $key       = TimBRCacheables::USER_BEARER.TimBRTestBook::SUCCESS_USER_NETWORK.TimBRTestBook::SUCCESS_USER;
            $ceaBearer = TimBRTestBook::getCeaBearer();
            Cache::put($key, $ceaBearer, 1440);

            $mock = \Mockery::mock(AuthenticationConnection::class)->makePartial();
            $mock->shouldReceive('authUser')->withAnyArgs()->andReturn($clientMocked);
            $mock->shouldReceive('getClient')->withAnyArgs()->andReturn($clientMocked);
            $mock->shouldReceive('getClientForOrder')->withAnyArgs()->andReturn($clientMocked);
            $mock->shouldReceive('getPMIDClient')->withAnyArgs()->andReturn($clientMocked);
            $mock->shouldReceive('authenticateNetwork')->withAnyArgs()->andReturn($clientMocked);

            return $mock;
        });

        $this->app->singleton(TimBRUserBearerHttp::class, function () use ($clientMocked, $bearer) {
            $mock = \Mockery::mock(TimBRUserBearerHttp::class)->makePartial();
            $mock->shouldReceive('encryptCpf')->withAnyArgs()->andReturn('Test');
            $mock->shouldReceive('requestBearer')->withAnyArgs()->andReturn($bearer);

            return $mock;
        });
    }
}
