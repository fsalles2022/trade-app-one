<?php

namespace McAfee\Connection;

use McAfee\Exceptions\McAfeeExceptions;
use SoapClient;
use SoapFault;
use TradeAppOne\Domain\Components\Helpers\XMLHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Soap\SoapNewClient;

class McAfeeSoapClient extends SoapNewClient
{
    public function __construct(SoapClient $client)
    {
        $this->client = $client;
    }

    public function execute($method, $arguments = [])
    {
        $start = microtime(true);
        try {
            $response = $this->client->$method(array("requestXML" => $arguments));
            heimdallLog()->realm(Operations::MCAFEE)
                ->start($start)
                ->end(microtime(true))
                ->request($arguments)
                ->response(XMLHelper::convertToArray($response->ProcessRequestWSResult))
                ->method($method)
                ->fire();
            return $response;
        } catch (SoapFault $exception) {
            heimdallLog()->realm(Operations::MCAFEE)
                ->start($start)
                ->end(microtime(true))
                ->request($arguments)
                ->catchException($exception)
                ->method($method)
                ->fire();
            throw McAfeeExceptions::mcAfeeUnavailable(json_encode($exception));
        }
    }
}
