<?php

namespace ClaroBR;

class SivHeaders
{
    private $uri;
    private $headers;
    private $customHeaders;
    private $token;

    public function __construct($configs)
    {
        $this->uri           = $configs['uri'];
        $this->headers       = $configs['headers'];
        $this->customHeaders = $configs['headers-by-context'];
        $this->token         = $configs['token'];
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }
}
