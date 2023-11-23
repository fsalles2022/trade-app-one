<?php


namespace ClaroBR\Connection;

use ClaroBR\Exceptions\VertexUnavailableException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class VertexHttpClient extends RestFulClient
{
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param $method
     * @param $url
     * @param $options
     * @return Response|ResponseInterface
     * @throws VertexUnavailableException
     * @throws GuzzleException
     */
    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        try {
            $response = $this->client->request($method, $url, $options);
            heimdallLog()->realm(Operations::VERTEX)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            return $response;
        } catch (ConnectException | ServerException | RequestException | ClientException $exception) {
            if ($exception instanceof ConnectException) {
                heimdallLog()->realm(Operations::VERTEX)
                    ->start($start)
                    ->end(microtime(true))
                    ->request($options)
                    ->catchException($exception)
                    ->url($url)
                    ->httpClient($this->client)
                    ->fire();
                throw new VertexUnavailableException($exception->getMessage());
            }

            $response = $exception->getResponse();
            if ($response) {
                heimdallLog()->realm(Operations::VERTEX)
                    ->start($start)
                    ->end(microtime(true))
                    ->request($options)
                    ->response($response)
                    ->url($url)
                    ->httpClient($this->client)
                    ->fire();
                return $response;
            }
            heimdallLog()->realm(Operations::VERTEX)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->catchException($exception)
                ->url($url)
                ->httpClient($this->client)
                ->fire();
            throw new VertexUnavailableException($exception->getMessage());
        }
    }
}
