<?php

namespace VivoBR\Connection\Headers;

use VivoBR\BaseSunHeaders;

class CeaSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers['cea'];
    }

    public function getToken(): string
    {
        return $this->headers['cea']['SUN-API-Token'];
    }
}
