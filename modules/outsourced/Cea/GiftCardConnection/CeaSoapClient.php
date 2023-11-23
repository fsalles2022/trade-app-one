<?php


namespace Outsourced\Cea\GiftCardConnection;

use Outsourced\Cea\Exceptions\CeaExceptions;
use Outsourced\Enums\Outsourced;
use SoapClient;
use SoapFault;
use TradeAppOne\Domain\Components\Helpers\ObjectHelper;
use TradeAppOne\Domain\HttpClients\Soap\SoapNewClient;

class CeaSoapClient extends SoapNewClient
{
    public const REALM = 'Outsource_' . Outsourced::CEA;

    protected $client;

    public function __construct(SoapClient $ceaClient)
    {
        $this->client = $ceaClient;
    }

    public function execute($method, $arguments = [])
    {
        $start = microtime(true);
        try {
            $response = $this->client->$method($arguments);
            heimdallLog()->realm(self::REALM)
                ->start($start)
                ->end(microtime(true))
                ->request(ObjectHelper::convertToJson($arguments))
                ->response(ObjectHelper::convertToJson($response))
                ->method($method)
                ->fire();
        } catch (SoapFault $exception) {
            heimdallLog()->realm(self::REALM)
                ->start($start)
                ->end(microtime(true))
                ->request(ObjectHelper::convertToJson($arguments))
                ->response(ObjectHelper::convertToJson($exception))
                ->method($method)
                ->fire();

            throw CeaExceptions::default($exception);
        }

        return $response;
    }
}
