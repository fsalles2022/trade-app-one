<?php


namespace Outsourced\Cea\tests\ServerTest;

use Mockery;
use Outsourced\Cea\ConsultaSerialConnection\CeaSerialConnection;
use Outsourced\Cea\tests\ServerTest\ConsultaSerial\CeaSerialResponseMock;
use SoapClient;

class CeaSerialServerMock
{
    public static function get()
    {
        $soapClient = Mockery::mock(SoapClient::class)->makePartial();

        $soapClient->shouldReceive(CeaSerialConnection::CONSULTAR_IMEI)
            ->andReturnUsing(static function ($imei) {
                return CeaSerialResponseMock::findDevice($imei);
            });

        return $soapClient;
    }
}
