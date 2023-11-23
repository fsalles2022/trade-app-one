<?php

namespace VivoBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use VivoBR\BaseSunHeaders;

class ExtraSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers[NetworkEnum::EXTRA];
    }

    public function getToken(): string
    {
        return $this->headers[NetworkEnum::EXTRA]['SUN-API-Token'];
    }
}
