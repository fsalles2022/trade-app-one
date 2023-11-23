<?php

declare(strict_types=1);

namespace Tradehub\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Outsourced\Enums\Outsourced;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class TradeHubHttpClient extends RestFulClient
{
    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);

        try {
            $response = $this->client->request($method, $url, $options);

            $this->dispatchResponseLog(
                $url,
                $response,
                $options,
                $start,
                microtime(true)
            );

            return $response;
        } catch (ConnectException | ServerException | RequestException | ClientException | GuzzleException $exception) {
            $this->dispatchResponseLog(
                $url,
                $exception->getResponse(),
                $options,
                $start,
                microtime(true)
            );

            return $exception->getResponse();
        }
    }

    protected function dispatchResponseLog(
        string $url,
        ?ResponseInterface $response = null,
        array $options = [],
        float $start = 0,
        float $end = 0,
        ?Throwable $exception = null
    ): void {

        $heimdallLog = heimdallLog()
            ->realm(Outsourced::TRADE_HUB)
            ->start($start)
            ->end($end)
            ->request($options)
            ->url($url)
            ->httpClient($this->client);

        if ($response !== null) {
            $heimdallLog->response($response);
        }

        if ($exception !== null) {
            $heimdallLog->catchException($exception);
        }

        $heimdallLog->fire();
    }
}
