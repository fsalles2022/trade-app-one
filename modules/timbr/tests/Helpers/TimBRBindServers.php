<?php

namespace TimBR\Tests\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use TimBR\Connection\TimBRElDorado\TimBRElDoradoHttpClient;
use TimBR\Connection\TimBRHttpClient;
use TimBR\Connection\TimExpress\TimBRExpressHttpClient;
use TimBR\Tests\ServerTest\ElDoradoServerMocked;
use TimBR\Tests\ServerTest\TimExpressServerMocked;
use TimBR\Tests\ServerTest\TimServerMocked;

trait TimBRBindServers
{
    public function bindTimResponse()
    {
        app()->bind(
            TimBRHttpClient::class,
            function () {
                $mock    = new TimServerMocked();
                $handler = HandlerStack::create($mock);
                $client  = new Client(['handler' => $handler]);
                return new TimBRHttpClient($client);
            }
        );

        $this->app->bind(TimBRElDoradoHttpClient::class, function () {
            $mock    = new ElDoradoServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new TimBRElDoradoHttpClient($client);
        });

        $this->app->bind(TimBRExpressHttpClient::class, function () {
            $mock    = new TimExpressServerMocked();
            $handler = HandlerStack::create($mock);
            $client  = new Client(['handler' => $handler]);
            return new TimBRExpressHttpClient($client);
        });
    }
}
