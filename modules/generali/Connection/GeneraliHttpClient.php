<?php


namespace Generali\Connection;

use Generali\Exceptions\GeneraliExceptions;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class GeneraliHttpClient extends RestFulClient
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
            heimdallLog()->realm(Operations::GENERALI)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (ClientException|ServerException $exception) {
            heimdallLog()
                ->realm(Operations::GENERALI)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->httpClient($this->client)
                ->url($url)
                ->response($exception->getResponse())
                ->fire();

            throw $exception;
        } catch (ConnectException $exception) {
            heimdallLog()
                ->realm(Operations::GENERALI)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->httpClient($this->client)
                ->url($url)
                ->catchException($exception)
                ->fire();

            throw GeneraliExceptions::unavailable($exception->getMessage());
        }
    }
}
