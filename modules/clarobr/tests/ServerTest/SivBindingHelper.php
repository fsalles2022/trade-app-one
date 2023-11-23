<?php

namespace ClaroBR\Tests\ServerTest;

use ClaroBR\Connection\SivHttpClient;
use ClaroBR\Services\MountNewAttributeFromSiv;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use TradeAppOne\Domain\Services\MountNewAttributesService;

trait SivBindingHelper
{
    private function bindMountNewAttributesFromSiv(): void
    {
        app()->bind(
            MountNewAttributeFromSiv::class,
            static function () {
                $mock = \Mockery::mock(MountNewAttributesService::class)->makePartial();
                $mock->shouldReceive('getAttributes')->withAnyArgs()->andReturn([]);
                return $mock;
            }
        );
    }

    public function bindSivResponse(): void
    {
        app()->bind(
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
