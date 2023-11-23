<?php

namespace Core\PowerBi\Connections;

use Core\PowerBi\Exceptions\PowerBiExceptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class PowerBiClient extends RestFulClient
{
    private const REALM = 'MicrosoftPowerBi';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        try {
            $response = $this->client->request($method, $url, $options);
            heimdallLog()->realm(self::REALM)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (ServerException $exception) {
            heimdallLog()
                ->start($start)
                ->end(microtime(true))
                ->realm(self::REALM)
                ->request($options)
                ->httpClient($this->client)
                ->url($url)
                ->response($exception->getResponse())
                ->fire();

            throw PowerBiExceptions::unavailable($exception);
        }
    }
}
