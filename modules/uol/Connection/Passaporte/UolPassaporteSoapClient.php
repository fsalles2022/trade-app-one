<?php

namespace Uol\Connection\Passaporte;

use SoapClient;
use TradeAppOne\Domain\Components\Helpers\XMLHelper;
use Uol\Adapters\Request\CancelarPassaporteRequestAdapter;
use Uol\Adapters\Request\ConfirmarPassaporteRequestAdapter;
use Uol\Adapters\Request\GerarPassaporteRequestAdapter;
use Uol\Connection\UolSoapClient;

class UolPassaporteSoapClient extends UolSoapClient
{
    const CONFIRMAR_PASSAPORTE = 'ConfirmarPassaporte';
    const GERAR_PASSPORTE      = 'GerarPassaporte';
    const CANCEL_PASSPORT      = 'CancelarPassaporte';

    public function __construct(SoapClient $client)
    {
        $this->client = $client;
    }

    public function confirmPassportGenerated(int $passportSerie)
    {
        $xmlParameters = ConfirmarPassaporteRequestAdapter::adapt($passportSerie);
        $response      = $this->execute(self::CONFIRMAR_PASSAPORTE, $xmlParameters);
        $xmlString     = $response->ConfirmarPassaporteResult->any;
        return XMLHelper::convertToArray($xmlString);
    }

    public function passportGenerated(int $passportType)
    {
        $xmlParameters = GerarPassaporteRequestAdapter::adapt($passportType);
        $response      = $this->execute(self::GERAR_PASSPORTE, $xmlParameters);
        $xmlString     = $response->GerarPassaporteResult->any;
        return  XMLHelper::convertToArray($xmlString);
    }

    public function cancelPassport(int $passportSerie)
    {
        $xmlParameters = CancelarPassaporteRequestAdapter::adapt($passportSerie);
        $response      = $this->execute(self::CANCEL_PASSPORT, $xmlParameters);
        $xmlString     = $response->CancelarPassaporteResult->any;
        return  XMLHelper::convertToArray($xmlString);
    }
}
