<?php

namespace VivoBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use VivoBR\BaseSunHeaders;

class FuijokaSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers[NetworkEnum::FUJIOKA];
    }

    public function getToken(): string
    {
        return $this->headers[NetworkEnum::FUJIOKA]['SUN-API-Token'];
    }
}
