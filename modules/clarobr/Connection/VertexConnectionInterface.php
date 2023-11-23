<?php


namespace ClaroBR\Connection;

use TradeAppOne\Domain\HttpClients\Responseable;

interface VertexConnectionInterface
{
    public function sendData(array $data);
}
