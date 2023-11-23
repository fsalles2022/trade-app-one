<?php


namespace Outsourced\Cea\tests\ServerTest;

use Outsourced\Cea\GiftCardConnection\CeaConnection;
use Outsourced\Cea\tests\ServerTest\GiftCard\CeaResponseMock;
use SoapClient;

class CeaGiftCardServerMock
{
    public static function get()
    {
        $soapClient = \Mockery::mock(SoapClient::class)->makePartial();

        $soapClient->shouldReceive(CeaConnection::GIFT_CARD_ACTIVATE)
            ->andReturn(CeaResponseMock::activate());

        return $soapClient;
    }
}
