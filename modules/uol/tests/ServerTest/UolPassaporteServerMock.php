<?php

namespace Uol\Tests\ServerTest;

use SoapClient;
use Uol\Connection\Passaporte\UolPassaporteSoapClient;
use Uol\Tests\ServerTest\Passaporte\CancelPassporte;
use Uol\Tests\ServerTest\Passaporte\ConfirmarPassaporte;
use Uol\Tests\ServerTest\Passaporte\GeneratePassport;

class UolPassaporteServerMock
{
    private $soapClient;

    public function __construct()
    {
        $this->soapClient = \Mockery::mock(SoapClient::class)->makePartial();

        $this->soapClient->shouldReceive(UolPassaporteSoapClient::GERAR_PASSPORTE)
            ->andReturn((new GeneratePassport())->getResult());

        $this->soapClient->shouldReceive(UolPassaporteSoapClient::CONFIRMAR_PASSAPORTE)
            ->andReturn((new ConfirmarPassaporte())->getResult())
            ->getMock();

        $this->soapClient->shouldReceive(UolPassaporteSoapClient::CANCEL_PASSPORT)
            ->andReturn((new CancelPassporte())->getResult())
            ->getMock();
    }

    public function getSoapClient(): SoapClient
    {
        return $this->soapClient;
    }
}
