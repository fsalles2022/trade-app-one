<?php

namespace VivoBR\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;
use VivoBR\BaseSunHeaders;

class CasaEVideoSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers[NetworkEnum::CASAEVIDEO];
    }

    public function getToken(): string
    {
        return $this->headers[NetworkEnum::CASAEVIDEO]['SUN-API-Token'];
    }
}
