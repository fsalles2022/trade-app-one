<?php

namespace Gateway\tests\ServerTest;

use Gateway\API\Gateway;
use Gateway\Helpers\GatewayMethodsEnum;
use Gateway\Tests\ServerTest\Methods\Authorize;
use Gateway\tests\ServerTest\Methods\Cancel;
use Gateway\tests\ServerTest\Methods\Sale;
use Gateway\Tests\ServerTest\Methods\Tokenize;
use Mockery;

class GatewayServerMock
{
    private $gateway;

    public function __construct()
    {
        $this->gateway = Mockery::mock(Gateway::class)->makePartial();
        $this->applyMock(GatewayMethodsEnum::SALE, new Sale());
        $this->applyMock(GatewayMethodsEnum::CANCEL, new Cancel());
        $this->applyMock(GatewayMethodsEnum::AUTHORIZE, new Authorize());
        $this->applyMock(GatewayMethodsEnum::TOKENIZE, new Tokenize());
    }

    public function getGateway(): Gateway
    {
        return $this->gateway;
    }

    public function applyMock(string $method, GatewayMethodInterface $gatewayMethod)
    {
        $this->gateway->shouldReceive($method)->andReturn($gatewayMethod->execute());
    }
}
