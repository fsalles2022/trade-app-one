<?php


namespace VivoBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use VivoBR\BaseSunHeaders;

class AvenidaSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers[NetworkEnum::AVENIDA];
    }

    public function getToken(): string
    {
        return $this->headers[NetworkEnum::AVENIDA]['SUN-API-Token'];
    }
}
