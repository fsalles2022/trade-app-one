<?php

namespace TradeAppOne\Domain\HttpClients\Soap;

abstract class SoapNewClient
{
    protected $client;

    abstract public function execute($method, $arguments);
}
