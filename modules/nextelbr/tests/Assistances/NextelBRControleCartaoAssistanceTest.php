<?php

namespace NextelBR\Tests\Assistances;

use NextelBR\Assistance\OperationAssistances\NextelBRControleCartaoAssistance;
use NextelBR\Connection\M4uModal\NextelBRModalConnection;
use NextelBR\Connection\NextelBR\NextelBRConnection;
use NextelBR\Tests\Helpers\NextelBRFactories;
use NextelBR\Models\NextelBRControleCartao;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\TestCase;

class NextelBRControleCartaoAssistanceTest extends TestCase
{
    use NextelBRFactories;

    /** @test */
    public function should_return_link()
    {
        $nextelModalConnection = resolve(NextelBRModalConnection::class);
        $nextelConnection      = resolve(NextelBRConnection::class);
        $saleRepository        = \Mockery::mock(SaleRepository::class)->makePartial();
        $service               = $this->factory()->of(NextelBRControleCartao::class)->make();

        $assistance = new NextelBRControleCartaoAssistance($nextelModalConnection, $nextelConnection, $saleRepository);
        $result     = $assistance->integrateService($service);
        self::assertArrayHasKey('link', $result->getAdapted());
    }

    /** @test */
    public function should_return_nextel_id_when_is_approved_by_modal_service_status_accepted()
    {
        $nextelModalConnection = resolve(NextelBRModalConnection::class);
        $nextelConnection      = resolve(NextelBRConnection::class);
        $saleRepository        = \Mockery::mock(SaleRepository::class)->makePartial();
        $service               = $this->factory()->of(NextelBRControleCartao::class)->make(['status' => ServiceStatus::ACCEPTED]);

        $assistance = new NextelBRControleCartaoAssistance($nextelModalConnection, $nextelConnection, $saleRepository);
        $result     = $assistance->integrateService($service, ['executed' => true]);
        self::assertArrayHasKey('message', $result->getAdapted());
        self::assertArrayHasKey('nextelIDExterno', $result->getAdapted());
    }

    /** @test */
    public function should_return_message_when_is_approved_by_modal_service_status_accepted()
    {
        $nextelModalConnection = resolve(NextelBRModalConnection::class);
        $nextelConnection      = resolve(NextelBRConnection::class);
        $saleRepository        = \Mockery::mock(SaleRepository::class)->makePartial();
        $service               = $this->factory()->of(NextelBRControleCartao::class)->make(['status' => ServiceStatus::ACCEPTED]);

        $assistance = new NextelBRControleCartaoAssistance($nextelModalConnection, $nextelConnection, $saleRepository);
        $result     = $assistance->integrateService($service, ['executed' => true]);
        self::assertArrayHasKey('message', $result->getAdapted());
    }

    /** @test */
    public function should_return_with_query_string()
    {
        $nextelModalConnection = resolve(NextelBRModalConnection::class);
        $nextelConnection      = resolve(NextelBRConnection::class);
        $saleRepository        = \Mockery::mock(SaleRepository::class)->makePartial();
        $service               = $this->factory()->of(NextelBRControleCartao::class)->make();

        $assistance = new NextelBRControleCartaoAssistance($nextelModalConnection, $nextelConnection, $saleRepository);
        $result     = $assistance->integrateService($service);
        self::assertTrue(str_contains($result->getAdapted()['link'], '?authCode='));
    }
}
