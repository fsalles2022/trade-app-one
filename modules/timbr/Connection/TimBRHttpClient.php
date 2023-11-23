<?php

namespace TimBR\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use TimBR\Exceptions\TimBRUnavailable;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;

class TimBRHttpClient extends RestFulClient
{
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function httpGet(string $uri, array $options)
    {
        return $this->execute('get', $uri, $options);
    }

    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        try {
            $response = $this->client->request($method, $url, $options);

            heimdallLog()->realm(Operations::TIM)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->fire();

            return $response;
        } catch (ServerException | ClientException $exception) {
            $response = $exception->getResponse();

            heimdallLog()->realm(Operations::TIM)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->fire();

            return $response;
        } catch (ConnectException $exception) {
            heimdallLog()->realm(Operations::TIM)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->catchException($exception)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            throw new TimBRUnavailable();
        }
    }

    public function httpPost(string $uri, array $options)
    {
        try {
            $response = $this->execute('post', $uri, $options);
            return RestResponse::success($response);
        } catch (ClientException|ServerException  $exception) {
            return RestResponse::failure($exception);
        }
    }
}
