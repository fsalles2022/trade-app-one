<?php

declare(strict_types=1);

namespace SurfPernambucanas\Tests\Unit\Services;

use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Response as HttpResponse;
use Mockery;
use Mockery\MockInterface;
use SurfPernambucanas\Adapters\PagtelActivationActivateResponseAdapter;
use SurfPernambucanas\Adapters\PagtelActivationPlansResponseAdapter;
use SurfPernambucanas\Adapters\PagtelAddCardResponseAdapter;
use SurfPernambucanas\Adapters\PagtelAllocatedMsisdnResponseAdapter;
use SurfPernambucanas\Adapters\PagtelCardsResponseAdapter;
use SurfPernambucanas\Adapters\PagtelPlansResponseAdapter;
use SurfPernambucanas\Adapters\PagtelResponseAdapter;
use SurfPernambucanas\Connection\PagtelConnection;
use SurfPernambucanas\DataObjects\CreditCardDTO;
use SurfPernambucanas\Services\PagtelService;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class PagtelServiceTest extends TestCase
{
    public function provider_adapter_and_methods_and_parameters_that_return_adapter(): array
    {
        return [
            [
                PagtelResponseAdapter::class,
                'subscriberActivate',
                '89550000000000000000',
                11,
                '04155519194',
            ],
            [
                PagtelAllocatedMsisdnResponseAdapter::class,
                'allocateMsisdn',
                '89550000000000000000',
            ],
            [
                PagtelPlansResponseAdapter::class,
                'plans',
                '5511999998888',
            ],
            [
                PagtelCardsResponseAdapter::class,
                'getCards',
                '5511999998888',
            ],
            [
                PagtelAddCardResponseAdapter::class,
                'addCard',
                '5511999998888',
                '123456789012345',
                '0722',
                '10',
                '23',
            ],
            [
                PagtelResponseAdapter::class,
                'recharge',
                '5511999998888',
                '5511999998882',
                '200',
                '5',
                '0722',
            ],
            [
                PagtelActivationPlansResponseAdapter::class,
                'activationPlans',
            ],
            [
                PagtelActivationActivateResponseAdapter::class,
                'activationActivate',
                '11',
                '8B7880FD-9B17-419D-9EDB-A0EC1E69C1C2',
                '8955170110240000347',
                '26797387809',
                'Jean Cigoli',
                new CreditCardDTO(
                    '41111111111111111',
                    '123',
                    '25',
                    '07'
                ),
                false,
            ],
        ];
    }

    /**
     * @dataProvider provider_adapter_and_methods_and_parameters_that_return_adapter
     * @param mixed $parameters
     */
    public function test_should_adapter_returned_by_adapter_and_method_and_parameters_when_connection_method_is_equals_service_method(
        string $adapterClass,
        string $method,
        ...$parameters
    ): void {
        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            '{}'
        );

        $this->mock_pagtel_connection(
            $method,
            RestResponse::success($responseMock)
        );

        $service = $this->resolve_pagtel_service();

        $response = $service->$method(...$parameters);

        $this->assertInstanceOf($adapterClass, $response);
    }

    private function resolve_pagtel_service(): PagtelService
    {
        return resolve(PagtelService::class);
    }

    private function mock_pagtel_connection(string $method, Responseable $response): void
    {
        $this->instance(
            PagtelConnection::class,
            Mockery::mock(PagtelConnection::class, function (MockInterface $mock) use ($method, $response): void {
                $mock->shouldReceive($method)
                    ->andReturn($response);
            })
        );
    }
}
