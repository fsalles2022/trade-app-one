<?php

namespace VivoBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use VivoBR\BaseSunHeaders;

class EletrozemaSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers[NetworkEnum::ELETROZEMA];
    }

    public function getToken(): string
    {
        return $this->headers[NetworkEnum::ELETROZEMA]['SUN-API-Token'];
    }
}
