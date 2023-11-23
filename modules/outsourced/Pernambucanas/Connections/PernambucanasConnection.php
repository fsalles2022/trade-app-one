<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Connections;

use Outsourced\Pernambucanas\Connections\Headers\PernambucanasHeaders;
use TradeAppOne\Domain\HttpClients\Responseable;

class PernambucanasConnection
{
    /** @var PernambucanasHttpClient */
    private $pernambucanasHttpClient;

    public function __construct(PernambucanasHttpClient $pernambucanasHttpClient)
    {
        $this->pernambucanasHttpClient = $pernambucanasHttpClient;
    }

    /**
     * @param mixed[] $payload
     * @return Responseable
     */
    public function saleRegister(array $payload): Responseable
    {
        $header = [
            'Authorization' => PernambucanasHeaders::getAuthorization(),
        ];

        return $this->pernambucanasHttpClient->post(
            PernambucanasRoutes::SALE_REGISTER,
            $payload,
            $header
        );
    }
}
