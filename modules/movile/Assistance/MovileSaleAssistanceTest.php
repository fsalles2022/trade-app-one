<?php

namespace Movile\Assistance;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Ring\Client\MockHandler;
use Movile\Connection\MovileConnection;
use Movile\Connection\MovileHttpClient;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\TestCase;
use function GuzzleHttp\Psr7\stream_for;

class MovileSaleAssistanceTest extends TestCase
{
    /** @test */
    public function should_call_update_service_with_approved_status()
    {
        $arrayResponse = ['subscription_id' => 12, 'account_id' => 12];
        $service       = factory(Service::class)->make();
        $response      = \Mockery::mock(RestResponse::class)->makePartial();
        $response->shouldReceive('toArray')->andReturn($arrayResponse);

        $connection = \Mockery::mock(MovileConnection::class)->makePartial();
        $connection->shouldReceive('subscribe')->andReturn($response);

        $repository = \Mockery::mock(SaleRepository::class)->makePartial();
        $repository->expects()
            ->updateService($service, ['status' => ServiceStatus::APPROVED])
            ->once()
            ->andReturn($service);
        $assistance = new MovileSaleAssistance($connection, $repository);
        $assistance->integrateService($service);
    }

    /** @test */
    public function should_call_update_service_with_status_rejected()
    {
        $arrayResponse = [];
        $service       = factory(Service::class)->make();
        $response      = \Mockery::mock(RestResponse::class)->makePartial();
        $response->shouldReceive('toArray')->andReturn($arrayResponse);

        $connection = \Mockery::mock(MovileConnection::class)->makePartial();
        $connection->shouldReceive('subscribe')->andReturn($response);

        $repository = \Mockery::mock(SaleRepository::class)->makePartial();
        $repository->expects()
            ->updateService($service, ['status' => ServiceStatus::REJECTED])
            ->once()
            ->andReturn($service);
        $assistance = new MovileSaleAssistance($connection, $repository);
        $assistance->integrateService($service);
    }
}
