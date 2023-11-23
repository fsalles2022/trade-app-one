<?php


namespace ClaroBR\Connection;

class VertexHeaders
{
    private $uri;
    private $headers;
    private $apiKey;
    private $token;

    public function __construct($configs)
    {
        $this->uri     = $configs['uri'];
        $this->headers = $configs['headers'];
        $this->apiKey  = $configs['headers']['x-api-key'];
        $this->token   = $configs['token'];
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
}
