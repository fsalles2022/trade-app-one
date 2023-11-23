<?php


namespace VivoBR\Connection\Headers;

use VivoBR\BaseSunHeaders;

class FastShopSunHeaders extends BaseSunHeaders
{
    public function getHeaders(): array
    {
        return $this->headers['fast_shop'];
    }

    public function getToken(): string
    {
        return $this->headers['fast_shop']['SUN-API-Token'];
    }
}
