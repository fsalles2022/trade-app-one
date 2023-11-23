<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Connection;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use SurfPernambucanas\Connection\PagtelConnection;
use SurfPernambucanas\Connection\PagtelHttpClient;
use SurfPernambucanas\Enumerators\PagtelInvoiceTypes;
use SurfPernambucanas\Tests\ServerTest\PagtelServerMocked;
use TradeAppOne\Exceptions\BuildExceptions;
use TradeAppOne\Tests\TestCase;

class PagtelConnectionTest extends TestCase
{
    public function test_should_authenticate_and_return_payload_with_access_token(): void
    {
        $this->mock_server_pagtel();

        $connection = $this->resolve_pagtel_connection();

        $response = $connection->authenticate();

        $this->assertTrue($response->isSuccess());

        $responseArray = $response->toArray();

        $this->assertArrayHasKey('authenticated', $responseArray);
        $this->assertArrayHasKey('created', $responseArray);
        $this->assertArrayHasKey('expiration', $responseArray);
        $this->assertArrayHasKey('expiration', $responseArray);
        $this->assertArrayHasKey('accessToken', $responseArray);
        $this->assertArrayHasKey('refreshToken', $responseArray);
        $this->assertArrayHasKey('message', $responseArray);
    }

    public function test_should_get_token_return_access_token(): void
    {
        $this->mock_server_pagtel();

        $connection = $this->resolve_pagtel_connection();

        $token = $connection->getToken();

        $this->assertNotEmpty($token);
    }

    public function test_should_not_authenticated_exception_when_return_authenticated_equals_false(): void
    {
        $this->mock_client_handler_with_response(new Response(
            200,
            [],
            '{"authenticated": false}'
        ));

        $this->expectException(BuildExceptions::class);
        
        $connection = $this->resolve_pagtel_connection();
        $connection->authenticate();
    }

    /** @return array[] */
    public function provider_methods_end_points_with_arg(): array
    {
        return [
            [
                'subscriberActivate',
                [
                    'ICCID'    => '89550000000000000000',
                    'areaCode' => '11',
                    'value'    => '04155519194',
                ],
            ],
            [
                'allocateMsisdn',
                [
                    'ICCID' => '89550000000000000000',
                ],
            ],
            [
                'plans',
                [
                    'Msisdn' => '5511998765432',
                ],
            ],
            [
                'getCards',
                [
                    'Msisdn' => '5511998765432',
                ],
            ],
            [
                'addCard',
                [
                    'paymentType' => PagtelInvoiceTypes::FLAGS[PagtelInvoiceTypes::CARTAO_CREDITO],
                    'Msisdn' => '5511998765432',
                    'cardNumber' => '41111111111111111',
                    'expiration' => '0725',
                    'cvv' => '123',
                ],
            ],
            [
                'recharge',
                [
                    'Msisdn' => '5511998765432',
                ],
            ],
            [
                'activationPlans',
            ],
            [
                'activationActivate',
                [
                    'area_code'   => '11',
                    'planId'      => '8B7880FD-9B17-419D-9EDB-A0EC1E69C1C2',
                    'iccid'       => '8955170110240000347',
                    'document'    => '26797387809',
                    'name'        => 'Jean Cigoli',
                    'recurrence'  => true,
                    'card' => [
                        'number'    => '41111111111111111',
                        'cvv'       => '123',
                        'validity'  => '0725',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provider_methods_end_points_with_arg
     * @param mixed[] $arg
     */
    public function test_should_sucess_when_called_method(string $method, array $arg = []): void
    {
        $this->mock_client_handler_with_response(
            new Response(
                HttpResponse::HTTP_OK,
                [],
                '{"authenticated": true, "accessToken": "123456789"}'
            ),
            new Response(
                HttpResponse::HTTP_OK,
                [],
                '{}'
            )
        );

        $connection = $this->resolve_pagtel_connection();

        $response = $connection->$method($arg);

        $this->assertTrue($response->isSuccess());
    }

    /**
     * @dataProvider provider_methods_end_points_with_arg
     * @param mixed[] $arg
     */
    public function test_should_exceptio_when_called_method(string $method, array $arg = []): void
    {
        $this->mock_client_handler_with_response(
            new Response(
                HttpResponse::HTTP_OK,
                [],
                '{"authenticated": true, "accessToken": "123456789"}'
            ),
            new Response(
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                [],
                '{}'
            )
        );

        $connection = $this->resolve_pagtel_connection();

        $connection->$method($arg);
    }

    private function resolve_pagtel_connection(): PagtelConnection
    {
        return resolve(PagtelConnection::class);
    }

    private function mock_server_pagtel(): void
    {
        $this->app->bind(PagtelHttpClient::class, function (): PagtelHttpClient {
            $mock = new PagtelServerMocked();

            return $this->init_http_client_by_mock($mock);
        });
    }

    private function mock_client_handler_with_response(ResponseInterface ...$response): void
    {
        $this->app->bind(PagtelHttpClient::class, function () use ($response): PagtelHttpClient {
            $mock = new MockHandler($response);

            return $this->init_http_client_by_mock($mock);
        });
    }

    /** @param mixed $mock */
    private function init_http_client_by_mock($mock): PagtelHttpClient
    {
        $handler = HandlerStack::create($mock);

        $client = new Client(['handler' => $handler]);

        return new PagtelHttpClient($client);
    }
}
