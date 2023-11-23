<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\Connection;

use TradeAppOne\Domain\HttpClients\Responseable;

class CasaEVideoConnection
{
    /** @var CasaEVideoHttpClient */
    private $casaEVideoHttpClient;

    public function __construct(CasaEVideoHttpClient $casaEVideoHttpClient)
    {
        $this->casaEVideoHttpClient = $casaEVideoHttpClient;
    }

    /** @param array $sale */
    public function sendSale(array $sale): Responseable
    {
        return $this->casaEVideoHttpClient->post(CasaEVideoRoutes::WEBOOK, $sale);
    }
}
