<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Outsourced\CasaEVideo\Exceptions\CasaEVideoExpcetions;
use Outsourced\Enums\Outsourced;
use Psr\Http\Message\ResponseInterface;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;

class CasaEVideoHttpClient extends RestFulClient
{
    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function execute($method, $url, $options): Response
    {
        $start = microtime(true);
        try {
            $response = $this->client->request($method, $url, $options);
            $this->generateLog($start, $options, $response, null, $url);
            return $response;
        } catch (ClientException|ServerException $exception) {
            $this->generateLog($start, $options, $exception->getResponse(), null, $url);
            throw $exception;
        } catch (ConnectException $exception) {
            $this->generateLog($start, $options, null, $exception, $url);
            throw CasaEVideoExpcetions::errorToSendSaleWebhook($exception->getMessage());
        }
    }

    /**
     * @param string|float $start
     * @param mixed[] $options
     */
    private function generateLog(
        $start,
        array $options,
        ?ResponseInterface $response,
        ?RequestException $exception,
        string $url
    ): void {
        $heimdallLog = heimdallLog()->realm('Outsourced_' . Outsourced::CASAEVIDEO)
            ->start($start)
            ->end(microtime(true))
            ->request($options)
            ->response($response ?? $exception->getResponse())
            ->url($url)
            ->httpClient($this->client);

        if ($exception !== null) {
            $heimdallLog->catchException($exception);
        }

        $heimdallLog->fire();
    }
}
