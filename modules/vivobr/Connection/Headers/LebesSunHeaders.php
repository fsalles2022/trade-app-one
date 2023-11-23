<?php

namespace VivoBR\Connection\Headers;

use VivoBR\BaseSunHeaders;

class LebesSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers['lebes'];
    }

    public function getToken(): string
    {
        return $this->headers['lebes']['SUN-API-Token'];
    }
}
