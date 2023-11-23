<?php

namespace Movile\Connection;

use Movile\Connection\Headers\MovileHeader;
use TradeAppOne\Domain\HttpClients\Responseable;

class MovileConnection
{
    protected $httpClient;

    public function __construct(MovileHttpClient $client)
    {
        $this->httpClient = $client;
    }

    public function subscribe(array $payload): Responseable
    {
        $bodyEncoded = json_encode($payload);
        $headers     = MovileHeader::getHeaders($bodyEncoded);
        return $this->httpClient->post(MovileRoutes::SUBSCRIBE, $payload, $headers);
    }
}
