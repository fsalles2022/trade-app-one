<?php

namespace VivoBR\Connection\Headers;

use VivoBR\BaseSunHeaders;

class RiachueloSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers['riachuelo'];
    }

    public function getToken(): string
    {
        return $this->headers['riachuelo']['SUN-API-Token'];
    }
}
