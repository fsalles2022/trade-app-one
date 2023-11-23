<?php

namespace OiBR\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use OiBR\Exceptions\OiBRUnavailable;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class OiBRHttpClient extends RestFulClient
{
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        $this->pushHeader(['Content-Type' => 'application/json']);
        try {
            $response = $this->client->request($method, $url, $options);
            heimdallLog()->realm(Operations::OI)->request($options)
                ->start($start)
                ->end(microtime(true))
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->method($method)
                ->fire();
            return $response;
        } catch (ClientException| ServerException $exception) {
            $response = $exception->getResponse();
            heimdallLog()->realm(Operations::OI)->request($options)
                ->start($start)
                ->end(microtime(true))
                ->response($response)
                ->url($url)
                ->method($method)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (ConnectException | RequestException $exception) {
            heimdallLog()->realm(Operations::OI)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->catchException($exception)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            throw new OiBRUnavailable($exception->getMessage());
        }
    }
}
