<?php

namespace VivoBR;

use VivoBR\Connection\Headers\SunHeader;

class BaseSunHeaders implements SunHeader
{
    protected $uri;
    protected $apiToken = '';
    protected $requestKey;
    protected $headers;
    protected $config;

    public function __construct(array $config)
    {
        $this->uri     = $config['uri'];
        $this->headers = $config['headers'];
        $this->config  = $config;
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
        return $this->headers;
    }
}
