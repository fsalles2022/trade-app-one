<?php

namespace NextelBR\Connection\M4uModal;

class NextelBRModalConnection
{
    protected $client;

    public function __construct(NextelBRModalHttpClient $client)
    {
        $this->client = $client;
    }

    public function getAuthenticationCode(array $payload)
    {
        return $this->client->post(NextelBRModalAPIRoutes::AUTH_CODE, $payload);
    }
}
