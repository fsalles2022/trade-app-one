<?php

namespace VivoBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use VivoBR\BaseSunHeaders;

class SchumannSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers[NetworkEnum::SCHUMANN];
    }

    public function getToken(): string
    {
        return $this->headers[NetworkEnum::SCHUMANN]['SUN-API-Token'];
    }
}
