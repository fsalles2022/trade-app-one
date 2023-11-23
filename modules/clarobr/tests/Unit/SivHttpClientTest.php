<?php

namespace ClaroBR\Tests\Unit;

use ClaroBR\Connection\SivHttpClient;
use ClaroBR\Exceptions\SivUnavailableException;
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

class SivHttpClientTest extends TestCase
{
    /** @test */
    public function post_should_return_unavailable_siv_exception_when_http_status_is_500()
    {
        $mock = new MockHandler([
            new Response(500),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client   = $this->app->make(SivHttpClient::class);
        $response = $client->post('a');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function get_should_return_unavailable_siv_exception_when_http_status_is_500()
    {
        $mock = new MockHandler([
            new Response(500),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client   = $this->app->make(SivHttpClient::class);
        $response = $client->get('a');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function put_should_return_unavailable_siv_exception_when_http_status_is_500()
    {
        $mock = new MockHandler([
            new Response(500),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client   = $this->app->make(SivHttpClient::class);
        $response = $client->put('a');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function authenticate_should_return_unavailable_siv_exception_when_http_status_is_500()
    {
        $mock = new MockHandler([
            new Response(500),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client   = $this->app->make(SivHttpClient::class);
        $response = $client->post('a');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function authenticate_should_return_unavailable_siv_exception_when_RequestExeption_throw()
    {
        $mock = new MockHandler([
            new RequestException(
                "Error Communicating with Server",
                new Request('GET', 'test'),
                new Response(\Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR, [], stream_for('{}'))
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client   = $this->app->make(SivHttpClient::class);
        $response = $client->post('a');
        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function authenticate_should_return_unavailable_siv_exception_when_ConnectExeption_throw()
    {
        $mock = new MockHandler([
            new ConnectException(
                "Error Communicating with Server",
                new Request('GET', 'test'),
                null
            )
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client = $this->app->make(SivHttpClient::class);
        $this->expectException(SivUnavailableException::class);
        $client->post('a');
    }

    /** @test */
    public function authenticate_should_return_reponse_content_when_ClientExeption_throw()
    {
        $mock = new MockHandler([
            new ClientException("Error Communicating with Server", new Request('GET', 'test'), new Response(
                \Illuminate\Http\Response::HTTP_MISDIRECTED_REQUEST,
                [],
                stream_for('{}')
            ))
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client   = $this->app->make(SivHttpClient::class);
        $response = $client->post('a');

        self::assertFalse($response->isSuccess());
    }

    /** @test */
    public function authenticate_should_return_reponse_status_421_when_ClientExeption_throw()
    {
        $mock = new MockHandler([
            new Response(
                \Illuminate\Http\Response::HTTP_MISDIRECTED_REQUEST,
                [],
                stream_for('{"data": {"token" : "sa"}}')
            ),
        ]);

        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $this->app->bind(SivHttpClient::class, function () use ($client) {
            return new SivHttpClient($client);
        });

        $client   = $this->app->make(SivHttpClient::class);
        $response = $client->post('a');
        self::assertEquals(\Illuminate\Http\Response::HTTP_MISDIRECTED_REQUEST, $response->getStatus());
    }
}
