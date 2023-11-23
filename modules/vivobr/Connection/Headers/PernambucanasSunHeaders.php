<?php

namespace VivoBR\Connection\Headers;

use VivoBR\BaseSunHeaders;

class PernambucanasSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers['pernambucanas'];
    }

    public function getToken(): string
    {
        return $this->headers['pernambucanas']['SUN-API-Token'];
    }
}
