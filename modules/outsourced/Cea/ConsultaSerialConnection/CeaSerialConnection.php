<?php


namespace Outsourced\Cea\ConsultaSerialConnection;

use Outsourced\Cea\Components\CeaSerialResponse;
use stdClass;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;

class CeaSerialConnection
{
    public const CONSULTAR_IMEI = 'ConsultaSerialTradeUp';

    protected $client;

    public function __construct(CeaSerialSoapClient $ceaClient)
    {
        $this->client = $ceaClient;
    }

    public function findDevice(string $imei) : array
    {
        $xmlParameters        = new stdClass();
        $xmlParameters->nuSer = str_pad($imei, 20, '0', STR_PAD_LEFT);

        $response = $this->client->execute(self::CONSULTAR_IMEI, $xmlParameters);

        return data_get(ObjectHelper::convertToArray($response), 'ConsultaSerialTradeUpResult.DadosSeriais', []);
    }
}
