<?php

declare(strict_types=1);

namespace TimBR\Connection\TimPremiumCommissioning;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use TimBR\Exceptions\TimBRCommissioningUnavailable;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class TimCommissioningHttpClient extends RestFulClient
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

            heimdallLog()->realm(Operations::TIM)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->response($response)
                ->url($url)
                ->httpClient($this->client)
                ->fire();

            return $response;
        } catch (ServerException | ClientException | ConnectException $exception) {
            heimdallLog()->realm(Operations::TIM)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->catchException($exception)
                ->url($url)
                ->httpClient($this->client)
                ->fire();

            if ($exception instanceof ConnectException) {
                throw new TimBRCommissioningUnavailable();
            }

            return $exception->getResponse();
        }
    }
}
