<?php

namespace VivoBR\Connection\Headers;

use VivoBR\BaseSunHeaders;

class HervalSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers['herval'];
    }

    public function getToken(): string
    {
        return $this->headers['herval']['SUN-API-Token'];
    }
}
