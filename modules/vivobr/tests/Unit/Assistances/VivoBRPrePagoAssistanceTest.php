<?php

namespace VivoBR\Tests\Unit\Assistances;

use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\TestCase;
use VivoBR\Assistances\VivoBRPreAssistance;
use VivoBR\Connection\SunConnection;
use VivoBR\Models\VivoControleCartao;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;

class VivoBRPrePagoAssistanceTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_return_m4u_url()
    {
        $response = [
            "codigo"   => 0,
            "idVenda"  => "RS-242498",
            "servicos" => [["id" => 276996]],
        ];

        $restResponse = \Mockery::mock(RestResponse::class)->makePartial();
        $restResponse->shouldReceive('toArray')->withAnyArgs()->andReturn($response);

        $connection = \Mockery::mock(SunConnection::class)->makePartial();
        $connection->shouldReceive('selectCustomConnection')->andReturnSelf();
        $connection->shouldReceive('sale')->andReturn($restResponse);

        $serviceCartao = $this->sunFactories()->of(VivoControleCartao::class)->make([
            'operation' => Operations::VIVO_PRE
        ])->toArray();

        $cartaoAssistance = \Mockery::mock(VivoBRPreAssistance::class, [
            $connection,
            resolve(SaleRepository::class)
        ])->makePartial();

        $sale = $this->saleFactory([$serviceCartao]);
        $sale->save();

        $result = $cartaoAssistance->integrateService($sale->services()->first());
        $result = json_decode($result->getContent(), true);
        self::assertArrayHasKey('message', $result);
        self::assertEquals(trans('sun::messages.activation.' . Operations::VIVO_PRE), $result['message']);
    }
}
