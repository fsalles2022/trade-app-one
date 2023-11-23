<?php

namespace OiBR\Connection\ElDoradoGateway;

class ElDoradoHeaders
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
        return ['apiToken' => $this->token];
    }
}
