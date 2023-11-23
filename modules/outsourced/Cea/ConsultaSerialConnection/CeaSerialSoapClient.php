<?php


namespace Outsourced\Cea\ConsultaSerialConnection;

use Outsourced\Cea\Exceptions\CeaExceptions;
use Outsourced\Enums\Outsourced;
use SoapClient;
use SoapFault;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;

class CeaSerialSoapClient
{
    public const REALM = 'Outsource_' . Outsourced::CEA;

    protected $client;

    public function __construct(SoapClient $ceaClient)
    {
        $this->client = $ceaClient;
    }

    public function execute($method, $arguments = [])
    {
        try {
            $start         = microtime(true);
            $response      = $this->client->$method($arguments);
            $end           = microtime(true);
            $executionTime = ($end - $start) / 60;
            heimdallLog()->realm(self::REALM)
                ->request(ObjectHelper::convertToJson($arguments))
                ->response(ObjectHelper::convertToJson($response))
                ->method($method)
                ->executionTime($executionTime)
                ->fire();
        } catch (SoapFault $exception) {
            heimdallLog()->realm(self::REALM)
                ->request(ObjectHelper::convertToJson($arguments))
                ->response(ObjectHelper::convertToJson($exception))
                ->method($method)
                ->fire();

            throw CeaExceptions::default($exception);
        }
        return $response;
    }
}
