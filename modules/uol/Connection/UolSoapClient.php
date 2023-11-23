<?php

namespace Uol\Connection;

use SoapFault;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Soap\SoapNewClient;
use Uol\Exceptions\UolExceptions;

class UolSoapClient extends SoapNewClient
{
    public function execute($method, $arguments = [])
    {
        $start = microtime(true);
        try {
            $response = $this->client->$method($arguments);
            heimdallLog()->realm(Operations::UOL)
                ->start($start)
                ->end(microtime(true))
                ->request($arguments)
                ->response($response)
                ->url($method)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (SoapFault $exception) {
            $response = $exception->getMessage();
            heimdallLog()->realm(Operations::UOL)
                ->start($start)
                ->end(microtime(true))
                ->request($arguments)
                ->response($response)
                ->url($method)
                ->httpClient($this->client)
                ->fire();
            throw UolExceptions::uolUnavailable();
        }
    }
}
