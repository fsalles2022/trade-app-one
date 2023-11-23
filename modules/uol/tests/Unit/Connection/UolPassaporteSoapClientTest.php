<?php

namespace TradeAppOne\Tests\Feature;

use SoapClient;
use SoapFault;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;
use Uol\Connection\Passaporte\UolPassaporteSoapClient;
use Uol\Exceptions\UolExceptions;

class UolPassaporteSoapClientTest extends TestCase
{
    use ArrayAssertTrait;
    private $uolPassaporteSoapClient;

    protected function setUp()
    {
        parent::setUp();
        $this->uolPassaporteSoapClient = resolve(UolPassaporteSoapClient::class);
    }

    /** @test */
    public function should_return_uol_unavailable_exception_when_client_is_down()
    {
        $soapFault  = new SoapFault("SOAP-ENV:Server", "There was a problem with the server, so the message could not proceed.");
        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->setMethods(array(UolPassaporteSoapClient::GERAR_PASSPORTE))
            ->disableOriginalConstructor()
            ->getMock();


        $soapClient->expects($this->once())
            ->method(UolPassaporteSoapClient::GERAR_PASSPORTE)
            ->will($this->throwException($soapFault));

        $uolPassaporteSoapClient = new UolPassaporteSoapClient($soapClient);
        $this->expectExceptionMessage(trans('uol::exceptions.'. UolExceptions::UOL_UNAVAILABLE));

        $uolPassaporteSoapClient->passportGenerated(2);
    }

    /** @test */
    public function should_return_valid_structure_when_call_confirm_passport_generated()
    {
        $response = $this->uolPassaporteSoapClient->confirmPassportGenerated(16029334);
        $this->assertArrayStructure($response, ['serie', 'numero', 'retorno', 'mensagem']);
    }

    /** @test */
    public function should_return_valid_structure_when_generated_passport()
    {
        $response = $this->uolPassaporteSoapClient->passportGenerated(1);
        $this->assertArrayStructure($response, ['serie', 'numero', 'retorno', 'mensagem']);
    }
}
