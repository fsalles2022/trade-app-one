<?php

namespace Outsourced\Riachuelo\Connections\Authentication;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Outsourced\Riachuelo\Connections\Headers\RiachueloHeaders;
use Outsourced\Riachuelo\Connections\RiachueloHttpClient;

class AuthenticationConnection
{
    const GRANT_TYPE  = ["grant_type" => "client_credentials"];
    const CACHE_TOKEN = "CACHE_RIACHUELO_AUTHENTICATION_TOKEN";

    protected $headers;

    public function __construct(RiachueloHeaders $headers)
    {
        $this->headers = $headers;
    }

    public function auth(): RiachueloHttpClient
    {
        $client = new Client([
            'verify'   => false,
            'base_uri' => $this->headers->getUri(),
            'headers'  => [
                'Authorization' => 'Basic ' . $this->headers->getBasicAuth(),
                'access_token'  => $this->getAccessToken(),
                'client_id'     => $this->headers->getClientId()
            ]
        ]);

        return $this->newHttpClient($client);
    }

    private function getAccessToken(): string
    {
        if ($access_token = Cache::get(self::CACHE_TOKEN)) {
            return $access_token;
        }

        $client = new Client([
            'verify'   => false,
            'base_uri' => $this->headers->getUri(),
            'headers'  => [
                'Authorization' => $this->headers->getBasicAuth(),
            ]
        ]);

        $client   = $this->newHttpClient($client);
        $response = $client->post(AuthenticationRoutes::AUTH, self::GRANT_TYPE)->toArray();

        $access_token = data_get($response, 'access_token', '');
        $expires_in   = data_get($response, 'expires_in', 3600) / 60;

        Cache::put(self::CACHE_TOKEN, $access_token, $expires_in);
        return $access_token;
    }

    public function newHttpClient($client): RiachueloHttpClient
    {
        return app()->makeWith(
            RiachueloHttpClient::class,
            ['client' => $client]
        );
    }
}
