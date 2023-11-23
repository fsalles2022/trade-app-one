<?php

namespace NextelBR\Connection\M4uModal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use NextelBR\Exceptions\NextelBRUnavailable;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class NextelBRModalHttpClient extends RestFulClient
{
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        try {
            $response = $this->client->request($method, $url, $options);
            heimdallLog()->realm(Operations::NEXTEL)
                ->start($start)
                ->end(microtime(true))
                ->url($url)
                ->method($method)
                ->request($options)
                ->response($response)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (ConnectException $exception) {
            heimdallLog()->realm(Operations::NEXTEL)
                ->start($start)
                ->end(microtime(true))
                ->url($url)
                ->method($method)
                ->request($options)
                ->catchException($exception)
                ->httpClient($this->client)
                ->fire();
            throw new NextelBRUnavailable();
        } catch (ClientException|ServerException $exception) {
            $response = $exception->getResponse();
            heimdallLog()->realm(Operations::NEXTEL)
                ->start($start)
                ->end(microtime(true))
                ->url($url)
                ->method($method)
                ->request($options)
                ->response($response)
                ->httpClient($this->client)
                ->fire();
            return $response;
        }
    }
}
