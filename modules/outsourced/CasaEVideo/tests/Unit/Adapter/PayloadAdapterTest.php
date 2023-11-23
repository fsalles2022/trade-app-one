<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\tests\Unit\Adapter;

use ClaroBR\Tests\Helpers\ClaroServices;
use Outsourced\CasaEVideo\Adapter\PayloadAdapter;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class PayloadAdapterTest extends TestCase
{
    public function test_should_check_if_service_is_adapted(): void
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

        $payloadAdapted = (new PayloadAdapter($sale->services->first()))->adapt()->toArray();

        $this->assertArrayHasKey('info', $payloadAdapted);
        $this->assertArrayHasKey('plano', $payloadAdapted);
        $this->assertArrayHasKey('aparelho', $payloadAdapted);
        $this->assertArrayHasKey('cliente', $payloadAdapted);
        $this->assertArrayHasKey('endereco', $payloadAdapted);
        $this->assertArrayHasKey('vendedor', $payloadAdapted);
        $this->assertArrayHasKey('campanha', $payloadAdapted);
        $this->assertArrayHasKey('dadosBancariosDebitoAutomatico', $payloadAdapted);
    }
}
