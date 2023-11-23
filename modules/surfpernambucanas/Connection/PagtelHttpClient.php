<?php

declare(strict_types=1);

namespace SurfPernambucanas\Connection;

use Carbon\Carbon;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use SurfPernambucanas\Exceptions\PagtelExceptions;
use Throwable;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\HttpClients\Restful\RestFulClient;
use TradeAppOne\Exceptions\BuildExceptions;

class PagtelHttpClient extends RestFulClient
{
    /** @var ClientInterface */
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param mixed[] $body
     * @param array[] $headers
     */
    public function post(
        string $url = '',
        array $body = [],
        array $headers = [],
        bool $includeTransactionId = true
    ): Responseable {
        if ($includeTransactionId === true) {
            $body = $this->includeTransactionIdInBody($body);
        }

        return parent::post($url, $body, $headers);
    }

    /**
     * @param mixed[] $body
     * @return mixed[]
     */
    protected function includeTransactionIdInBody(array $body): array
    {
        $body['transactionID'] = Carbon::now()->format('YmdHis');

        return $body;
    }

    /**
     * @inheritDoc
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     * @throws BuildExceptions
     */
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
        } catch (ConnectException | ServerException | RequestException | ClientException $exception) {
            $this->dispatchResponseLog(
                $url,
                $exception->getResponse(),
                $options,
                $start,
                microtime(true),
                $exception
            );
            return $exception->getResponse();
        }
    }

    /** @param mixed[] $options */
    protected function dispatchResponseLog(
        string $url,
        ?ResponseInterface $response = null,
        array $options = [],
        float $start = 0,
        float $end = 0,
        ?Throwable $exception = null
    ): void {
        $heimdallLog = heimdallLog()
            ->realm(Operations::SURF_PERNAMBUCANAS)
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
