<?php

namespace Outsourced\Partner\Connections;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Outsourced\Enums\Outsourced;
use Outsourced\Partner\Exceptions\PartnerExceptions;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class PartnerHttpClient extends RestFulClient
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
            heimdallLog()->realm('Outsourced_' . Outsourced::PARTNER)
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
                ->realm('Outsource_' . Outsourced::PARTNER)
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
                ->realm('Outsource_' . Outsourced::PARTNER)
                ->start($start)
                ->end(microtime(true))
                ->request($options)
                ->httpClient($this->client)
                ->url($url)
                ->catchException($exception)
                ->fire();

            throw PartnerExceptions::unavailable($exception->getMessage());
        }
    }
}
