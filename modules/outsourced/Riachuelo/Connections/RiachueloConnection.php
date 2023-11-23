<?php

namespace Outsourced\Riachuelo\Connections;

use Outsourced\Riachuelo\Connections\Authentication\AuthenticationConnection;

class RiachueloConnection
{
    protected $client;

    public function __construct(RiachueloHttpClient $httpClient)
    {
        $this->client = $httpClient;
    }

    public function findDevice(string $imei): array
    {
        return $this->client
            ->get(RiachueloRoutes::deviceByImei($imei))
            ->toArray();
    }
}
