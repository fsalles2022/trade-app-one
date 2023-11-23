<?php

namespace VivoBR\Tests\Unit\Assistances;

use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\HttpClients\Restful\RestResponse;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\TestCase;
use VivoBR\Assistances\VivoBRControleCartaoAssistance;
use VivoBR\Connection\SunConnection;
use VivoBR\Models\VivoControleCartao;
use VivoBR\Tests\Helpers\VivoFactoriesHelper;

class VivoBRControleCartaoAssistanceTest extends TestCase
{
    use VivoFactoriesHelper;

    /** @test */
    public function should_return_m4u_url()
    {
        $response = [
            "codigo"      => 0,
            "idVenda"     => "RS-242498",
            "servicos"    => [["id" => 276996]],
            "urlM4U"      => "https:\/\/vivocontrole.stg.m4u",
            "urlM4UPlana" => "https:\/\/vivocontrole.stg.m4u.com.br\/varejo?k="
        ];

        $restResponse = \Mockery::mock(RestResponse::class)->makePartial();
        $restResponse->shouldReceive('toArray')->withAnyArgs()->andReturn($response);

        $connection = \Mockery::mock(SunConnection::class)->makePartial();
        $connection->shouldReceive('selectCustomConnection')->andReturnSelf();
        $connection->shouldReceive('sale')->andReturn($restResponse);

        $serviceCartao = $this->sunFactories()->of(VivoControleCartao::class)->make()->toArray();

        $cartaoAssistance = \Mockery::mock(VivoBRControleCartaoAssistance::class, [
            $connection,
            resolve(SaleRepository::class)
        ])->makePartial();

        $sale = $this->saleFactory([$serviceCartao]);
        $sale->save();

        $result = $cartaoAssistance->integrateService($sale->services()->first());
        $result = json_decode($result->getContent(), true);
        self::assertArrayHasKey('urlM4U', $result['data']);
    }

    /** @test */
    public function should_not_call_sun_but_return_m4u_url()
    {
        $log = [
            [
                "codigo"      => 0,
                "idVenda"     => "RS-242498",
                "servicos"    => [["id" => 276996]],
                "urlM4U"      => "https:\/\/vivocontrole.stg.m4u",
                "urlM4UPlana" => "https:\/\/vivocontrole.stg.m4u.com.br\/varejo?k="
            ]
        ];


        $connection = \Mockery::mock(SunConnection::class)->makePartial();
        $connection->shouldReceive('selectCustomConnection')->never();

        $serviceCartao = $this->sunFactories()
            ->of(VivoControleCartao::class)
            ->make([
                'log'    => $log,
                'status' => ServiceStatus::SUBMITTED
            ])->toArray();

        $cartaoAssistance = \Mockery::mock(VivoBRControleCartaoAssistance::class, [
            $connection,
            resolve(SaleRepository::class)
        ])->makePartial();

        $sale = $this->saleFactory([$serviceCartao]);
        $sale->save();

        $result = $cartaoAssistance->integrateService($sale->services->first());
        self::assertArrayHasKey('urlM4U', $result['data']);
    }
}
