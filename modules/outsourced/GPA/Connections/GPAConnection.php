<?php


namespace Outsourced\GPA\Connections;

use TradeAppOne\Domain\HttpClients\Responseable;

class GPAConnection
{
    private $gPAHttpClient;

    public function __construct(GPAHttpClient $gPAHttpClient)
    {
        $this->gPAHttpClient = $gPAHttpClient;
    }

    /**
     * @param mixed[] $payload
     * @return Responseable
     */
    public function saleRegister(array $payload): Responseable
    {
        return $this->gPAHttpClient->post(GPARoutes::SALE_REGISTER, $payload);
    }
}
