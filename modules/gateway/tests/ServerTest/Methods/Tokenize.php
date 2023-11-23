<?php


namespace Gateway\Tests\ServerTest\Methods;

use Gateway\API\Tokenization;
use Gateway\tests\ServerTest\GatewayMethodInterface;

class Tokenize implements GatewayMethodInterface
{
    public const TOKEN = '4370f54683c461a182d9914d9d7581bb0308cbf404b8bf473d298febd1bb4d96';

    public function execute()
    {
        $mock = \Mockery::mock(Tokenization::class)->makePartial();
        $mock->shouldReceive('getTokenCard')->andReturn(self::TOKEN);

        return $mock;
    }
}
