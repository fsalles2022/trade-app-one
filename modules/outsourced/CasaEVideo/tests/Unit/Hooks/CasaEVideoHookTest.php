<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\tests\Unit\Hooks;

use ClaroBR\Tests\Helpers\ClaroServices;
use Outsourced\CasaEVideo\Hooks\CasaEVideoHook;
use Outsourced\CasaEVideo\tests\ServerMock\Apis\CasaEVideoHookApiResponse;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class CasaEVideoHookTest extends TestCase
{
    public function test_should_to_send_sale_casa_e_video_hook_success(): void
    {
        $network         = (new NetworkBuilder())->withSlug('casaevideo')->build();
        $pointOfSale     = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $salesmanCompany = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();

        $claroControleFacilSale         = ClaroServices::ControleFacil();
        $claroControleFacilSale->status = ServiceStatus::ACCEPTED;

        $sale = (new SaleBuilder())
            ->withUser($salesmanCompany)
            ->withServices($claroControleFacilSale)
            ->build();

        $casaEVideoHook = resolve(CasaEVideoHook::class);
        $saleIsSend     = $casaEVideoHook->execute($sale->services->first());

        $this->assertTrue($saleIsSend);
    }

    public function test_should_to_send_sale_casa_e_video_hook_failed(): void
    {
        $network         = (new NetworkBuilder())->withSlug('casaevideo')->build();
        $pointOfSale     = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $salesmanCompany = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();

        $salesmanCompany->cpf = CasaEVideoHookApiResponse::CPF_FAILED;

        $claroControleFacilSale         = ClaroServices::ControleFacil();
        $claroControleFacilSale->status = ServiceStatus::ACCEPTED;

        $sale = (new SaleBuilder())
            ->withUser($salesmanCompany)
            ->withServices($claroControleFacilSale)
            ->build();

        $casaEVideoHook = resolve(CasaEVideoHook::class);
        $saleIsSend     = $casaEVideoHook->execute($sale->services->first());

        $this->assertFalse($saleIsSend);
    }
}
