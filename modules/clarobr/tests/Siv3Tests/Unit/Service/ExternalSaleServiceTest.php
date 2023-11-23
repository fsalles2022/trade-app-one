<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Unit\Service;

use ClaroBR\Connection\Siv3Connection;
use ClaroBR\Services\ExternalSaleService;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Http\Response as HttpResponse;
use TradeAppOne\Domain\HttpClients\Responseable;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Tests\TestCase;

class ExternalSaleServiceTest extends TestCase
{
    public function provider_methods_and_parameters(): array
    {
        return [
            [
                'checkExternalSaleExist',
                '04155519194',
            ],
        ];
    }

    /**
     * @dataProvider provider_methods_and_parameters
     * @param mixed $parameters
     */
    public function test_should_return_an_array_response(
        string $method,
        ...$parameters
    ): void {

        $responseMock = new Response(
            HttpResponse::HTTP_OK,
            [],
            '{}'
        );

        $service = $this->resolve_external_sale__service();

        $this->mock_siv3_connection(
            $method,
            RestResponse::success($responseMock)
        );

        $response = $service->$method($parameters);

        $this->assertArrayHasKey('response', $response);
    }

    private function resolve_external_sale__service(): ExternalSaleService
    {
        return resolve(ExternalSaleService::class);
    }

    private function mock_siv3_connection(string $method, Responseable $response): void
    {
        $this->instance(
            Siv3Connection::class,
            Mockery::mock(
                Siv3Connection::class,
                function (MockInterface $mock) use ($method, $response): void {
                    $mock->shouldReceive($method)
                        ->andReturn($response);
                }
            )
        );
    }
}
