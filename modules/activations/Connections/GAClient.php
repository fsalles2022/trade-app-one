<?php

namespace GA\Connections;

use GA\Exceptions\ActivationsExceptions;
use GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class GAClient extends RestFulClient
{
    public const GATEWAY_ACTIVATIONS = 'GATEWAY_ACTIVATIONS';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        try {
            $response = $this->client->request($method, $url, $options);
            heimdallLog()->realm(self::GATEWAY_ACTIVATIONS)
                ->start($start)
                ->end(microtime(true))
                ->response($response)
                ->method($method)
                ->fire();
        } catch (ClientException $exception) {
            heimdallLog()->realm(self::GATEWAY_ACTIVATIONS)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->httpClient($this->client)
                ->url($url)
                ->catchException($exception)
                ->fire();

            throw ActivationsExceptions::unavailable($exception->getMessage());
        }

        return $response;
    }
}
