<?php

declare(strict_types=1);

namespace Tradehub\Tests\Unit\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use Psr\Http\Message\ResponseInterface;
use TradeAppOne\Tests\TestCase;
use Tradehub\Connection\TradeHubHttpClient;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class TradeHubHttpClientTest extends TestCase
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
    public function test_method_should_success_without_exception(string $method): void
    {
        $httpClient = $this->create_http_client_with_mock_status_200();

        $response = $httpClient->$method('/test');

        $this->assertEquals(ResponseAlias::HTTP_OK, $response->getStatus());
    }

    public function create_http_client_with_mock_status_200(): TradeHubHttpClient
    {
        $this->mock_client_handler_with_response(new Response(
            ResponseAlias::HTTP_OK,
            [],
            '{}'
        ));

        return resolve(TradeHubHttpClient::class);
    }

    /** @dataProvider provider_methods */
    public function test_method_should_return_unavailable_tradehub_exception_when_have_server_exception(string $method): void
    {
        $httpClient = $this->create_http_client_with_mock_status_500();

        $httpClient->$method('/test');
    }

    private function create_http_client_with_mock_status_500(): TradeHubHttpClient
    {
        $this->mock_client_handler_with_response(new Response(
            ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
            [],
            '{}'
        ));

        return resolve(TradeHubHttpClient::class);
    }

    private function mock_client_handler_with_response(ResponseInterface $response): void
    {
        $this->app->bind(TradeHubHttpClient::class, function () use ($response): TradeHubHttpClient {

            $mock = new MockHandler([$response]);

            $handler = HandlerStack::create($mock);

            $client = new Client(['handler' => $handler]);

            return new TradeHubHttpClient($client);
        });
    }
}
