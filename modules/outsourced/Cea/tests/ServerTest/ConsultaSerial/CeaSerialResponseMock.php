<?php


namespace Outsourced\Cea\tests\ServerTest\ConsultaSerial;

use Outsourced\Cea\tests\CeaEnumTest;
use stdClass;

class CeaSerialResponseMock
{
    public static function findDevice(stdClass $imei): stdClass
    {
        $response = new stdClass();
        if ($imei->nuSer !== CeaEnumTest::DEVICE_IMEI) {
            return $response;
        }
        $response->ConsultaSerialTradeUpResult               = new stdClass();
        $response->ConsultaSerialTradeUpResult->DadosSeriais = new stdClass();

        $response->ConsultaSerialTradeUpResult->DadosSeriais->IMEI   = '00000000000000000110';
        $response->ConsultaSerialTradeUpResult->DadosSeriais->MODELO = 'SAMSUNG GALAXY NOTE 9';
        $response->ConsultaSerialTradeUpResult->DadosSeriais->DESCR  = 'SAMSUNG GALAXY NOTE 9 128 GB';
        $response->ConsultaSerialTradeUpResult->DadosSeriais->SKU    = '9266406';
        $response->ConsultaSerialTradeUpResult->DadosSeriais->PRECO  = '11431.14';

        return $response;
    }
}
