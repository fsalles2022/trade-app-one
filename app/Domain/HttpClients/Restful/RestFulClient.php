<?php

namespace TradeAppOne\Domain\HttpClients\Restful;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Domain\HttpClients\HttpClientBehavior;
use TradeAppOne\Domain\HttpClients\Responseable;

/**
 * @property Client $client
 */
abstract class RestFulClient implements HttpClientBehavior
{
    protected $client;
    protected $routes;
    protected $headers = [];

    public function pushHeader(array $header): void
    {
        $this->headers = array_merge($this->headers, $header);
    }

    public function get(string $url, array $query = [], array $headers = []): Responseable
    {
        try {
            $response = $this->execute(__FUNCTION__, $url, ['headers' => $this->headers, 'query' => $query]);
            return RestResponse::success($response);
        } catch (ClientException|ServerException  $exception) {
            return RestResponse::failure($exception);
        }
    }

    abstract public function execute($method, $url, $options): Response;

    public function put(string $url = '', array $body = [], array $header = []): Responseable
    {
        try {
            $response = $this->execute(__FUNCTION__, $url, ['headers' => $this->headers, 'json' => $body]);
            return RestResponse::success($response);
        } catch (ClientException|ServerException  $exception) {
            return RestResponse::failure($exception);
        }
    }

    public function delete(string $url = '', array $body = [], array $header = []): Responseable
    {
        try {
            $response = $this->execute(__FUNCTION__, $url, ['json' => $body]);
            return RestResponse::success($response);
        } catch (ClientException|ServerException  $exception) {
            return RestResponse::failure($exception);
        }
    }

    public function post(string $url = '', array $body = [], array $headers = []): Responseable
    {
        try {
            $this->headers = array_merge($this->headers, $headers);
            $response      = $this->execute(__FUNCTION__, $url, ['headers' => $this->headers, 'json' => $body]);

            return RestResponse::success($response);
        } catch (ClientException|ServerException  $exception) {
            return RestResponse::failure($exception);
        }
    }

    public function postFormParams(string $url = '', array $form = [], array $headers = []): Responseable
    {
        try {
            $response = $this->execute('post', $url, ['headers' => $headers, 'form_params' => $form]);
            return RestResponse::success($response);
        } catch (ClientException|ServerException  $exception) {
            return RestResponse::failure($exception);
        }
    }
}
