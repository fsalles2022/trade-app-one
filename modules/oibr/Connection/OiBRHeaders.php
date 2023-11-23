<?php

namespace OiBR\Connection;

class OiBRHeaders
{
    private $uri;
    private $token;

    public function __construct(array $config)
    {
        $this->uri   = $config['uri'];
        $this->token = $config['apiToken'];
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return ['x-api-key' => $this->token, 'Content-Type' => 'application/json'];
    }
}
