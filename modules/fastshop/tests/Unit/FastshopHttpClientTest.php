<?php

namespace FastShop\tests\Unit;

use ClaroBR\Connection\SivHttpClient;
use FastShop\Connection\FastshopHttpClient;
use FastShop\Exceptions\FastshopUnavailableException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class FastshopHttpClientTest extends TestCase
{
    /** @test */
    public function post_should_return_unavailable_fastshop_exception_when_have_server_exception(): void
    {
        $this->expectException(FastshopUnavailableException::class);
        $this->mockFastshopEnpointResponse();

        $client   = $this->app->make(FastshopHttpClient::class);
        $response = $client->post('test');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function get_should_return_unavailable_fastshop_exception_when_have_server_exception(): void
    {
        $this->expectException(FastshopUnavailableException::class);
        $this->mockFastshopEnpointResponse();

        $client   = $this->app->make(FastshopHttpClient::class);
        $response = $client->get('test');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function put_should_return_unavailable_fastshop_exception_when_have_server_exception(): void
    {
        $this->expectException(FastshopUnavailableException::class);
        $this->mockFastshopEnpointResponse();

        $client   = $this->app->make(FastshopHttpClient::class);
        $response = $client->put('test');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function authenticate_should_return_unavailable_fastshop_exception_when_RequestExeption_throw(): void
    {
        $this->expectException(FastshopUnavailableException::class);
        $this->mockFastshopEnpointResponse(
            RequestException::class,
            'Error Communicating with Server'
        );

        $client   = $this->app->make(FastshopHttpClient::class);
        $response = $client->post('test');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function authenticate_should_return_unavailable_fastshop_exception_when_ConnectExeption_throw()
    {
        $this->expectException(FastshopUnavailableException::class);
        $this->mockFastshopEnpointResponse(
            ConnectException::class,
            'Error Communicating with Server'
        );

        $client = $this->app->make(FastshopHttpClient::class);
        $client->post('a');
    }

    /** @test */
    public function authenticate_should_return_reponse_content_when_ClientExeption_throw()
    {
        $this->expectException(FastshopUnavailableException::class);
        $this->mockFastshopEnpointResponse(
            ClientException::class,
            'Error Communicating with Server'
        );

        $client   = $this->app->make(FastshopHttpClient::class);
        $response = $client->post('a');

        self::assertFalse($response->isSuccess());
    }

    private function mockFastshopEnpointResponse($className = null, $message = null, $responseStatusCode = 500): void
    {
        $response = $className !== null ?
            new $className($message, new Request('GET', 'test'), null) :
            new Response($responseStatusCode);

        $mock = new MockHandler([$response]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(FastshopHttpClient::class, static function () use ($client) {
            return new FastshopHttpClient($client);
        });
    }
}
