<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Unit\Connection;

use ClaroBR\Connection\Siv3HttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Siv3HttpClientTest extends TestCase
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

    /** @dataProvider provider_methods */
    public function test_method_should_return_unavailable_siv3_exception_when_have_server_exception(string $method): void
    {
        $httpClient = $this->create_http_client_with_mock_status_500();

        $httpClient->$method('/test');
    }

    private function create_http_client_with_mock_status_500(): Siv3HttpClient
    {
        $this->mock_client_handler_with_response(new Response(
            ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
            [],
            '{}'
        ));

        return resolve(Siv3HttpClient::class);
    }

    public function create_http_client_with_mock_status_200(): Siv3HttpClient
    {
        $this->mock_client_handler_with_response(new Response(
            ResponseAlias::HTTP_OK,
            [],
            '{}'
        ));

        return resolve(Siv3HttpClient::class);
    }

    private function mock_client_handler_with_response(ResponseInterface $response): void
    {
        $this->app->bind(Siv3HttpClient::class, function () use ($response): Siv3HttpClient {

            $mock = new MockHandler([$response]);

            $handler = HandlerStack::create($mock);

            $client = new Client(['handler' => $handler]);

            return new Siv3HttpClient($client);
        });
    }
}
