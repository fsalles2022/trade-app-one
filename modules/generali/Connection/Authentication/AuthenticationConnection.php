<?php


namespace Generali\Connection\Authentication;

use Generali\Connection\GeneraliHttpClient;
use Generali\Connection\Headers\GeneraliHeaders;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class AuthenticationConnection
{
    private const CACHE_TOKEN = "AUTHENTICATION_TOKEN_GENERALI";

    public function auth()
    {
        $client = new Client([
            'verify'   => false,
            'base_uri' => GeneraliHeaders::getUri(),
            'headers'  => [
                'APIKEY'  => $this->getAccessToken(),
            ]
        ]);

        return $this->newHttpClient($client);
    }

    private function getAccessToken(): string
    {
        if ($apiKey = Cache::get(self::CACHE_TOKEN)) {
            return $apiKey;
        }

        $client   = new Client(['verify'   => false, 'base_uri' => GeneraliHeaders::getUri()]);
        $response = $this->newHttpClient($client)->get(AuthenticationRoutes::AUTH, [
            'email' => GeneraliHeaders::getMail(),
            'senha' => GeneraliHeaders::getPassword(),
        ])->toArray();

        $apiKey = Arr::get($response, 'api_key', '');

        Cache::put(self::CACHE_TOKEN, $apiKey, 300);

        return $apiKey;
    }

    private function newHttpClient($client)
    {
        return app()->makeWith(GeneraliHttpClient::class, ['client' => $client]);
    }
}
