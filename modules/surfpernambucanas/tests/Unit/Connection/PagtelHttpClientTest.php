<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use SurfPernambucanas\Connection\PagtelHttpClient;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\TestCase;

class PagtelHttpClientTest extends TestCase
{
    /** @return array[] */
    public function provider_methods(): array
    {
        return [
            ['get'],
            ['post'],
            ['put'],
            ['delete'],
        ];
    }

    /** @dataProvider provider_methods */
    public function test_method_Should_return_unavailable_pagtel_exception_when_have_server_exception(string $method): void
    {
        $httpClient = $this->create_http_client_with_mock_status_500();

        $httpClient->$method('/test');
    }

    /** @dataProvider provider_methods */
    public function test_method_should_success_without_exception(string $method): void
    {
        $httpClient = $this->create_http_client_with_mock_status_200();

        $response = $httpClient->$method('/test');

        $this->assertEquals(HttpResponse::HTTP_OK, $response->getStatus());
    }

    private function create_http_client_with_mock_status_500(): PagtelHttpClient
    {
        $this->mock_client_handler_with_response(new Response(
            HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
            [],
            '{}'
        ));

        return resolve(PagtelHttpClient::class);
    }

    public function create_http_client_with_mock_status_200(): PagtelHttpClient
    {
        $this->mock_client_handler_with_response(new Response(
            HttpResponse::HTTP_OK,
            [],
            '{}'
        ));

        return resolve(PagtelHttpClient::class);
    }

    private function mock_client_handler_with_response(ResponseInterface $response): void
    {
        $this->app->bind(PagtelHttpClient::class, function () use ($response): PagtelHttpClient {
            $mock = new MockHandler([$response]);

            $handler = HandlerStack::create($mock);

            $client = new Client(['handler' => $handler]);

            return new PagtelHttpClient($client);
        });
    }
}
