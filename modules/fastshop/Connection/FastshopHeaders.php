<?php

namespace FastShop\Connection;

class FastshopHeaders
{
    private $uri;
    private $headers;
    private $clientId;
    private $clientSecret;
    private $grantType;
    private $timeoutConnection;
    private $verifyConnection;

    private const DEFAULT_TIMEOUT_CONNECTION = 20.0;
    private const DEFAULT_VERIFY_CONNECTION  = false;

    public function __construct($configs)
    {
        $this->uri               = $configs['uri'];
        $this->headers           = $configs['headers'];
        $this->grantType         = $configs['grant_type'];
        $this->clientId          = $configs['client_id'];
        $this->clientSecret      = $configs['client_secret'];
        $this->timeoutConnection = self::DEFAULT_TIMEOUT_CONNECTION;
        $this->verifyConnection  = self::DEFAULT_VERIFY_CONNECTION;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getTimeoutConnection(): float
    {
        return $this->timeoutConnection;
    }

    public function getVerifyConnection(): bool
    {
        return $this->verifyConnection;
    }
}
