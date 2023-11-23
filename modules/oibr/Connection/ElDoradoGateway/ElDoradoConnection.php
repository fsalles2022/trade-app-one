<?php

namespace OiBR\Connection\ElDoradoGateway;

use TradeAppOne\Domain\HttpClients\Responseable;

class ElDoradoConnection
{
    protected $client;

    public function __construct(ElDoradoHttpClient $client)
    {
        $this->client = $client;
    }

    public function getCreditCards(string $msisdn): Responseable
    {
        return $this->client->get(ElDoradoRoutes::queryCreditCard($msisdn));
    }
}
