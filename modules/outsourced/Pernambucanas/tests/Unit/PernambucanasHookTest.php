<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\tests\Unit;

use ClaroBR\Tests\Helpers\ClaroServices;
use Outsourced\Pernambucanas\Connections\PernambucanasConnection;
use Outsourced\Pernambucanas\Hooks\PernambucanasHook;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class PernambucanasHookTest extends TestCase
{
    /** @test */
    public function should_send_sale_to_company_by_webhook_factory()
    {
        $network         = (new NetworkBuilder())->withSlug('pernambucanas')->build();
        $pointOfSale     = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $salesmanCompany = (new UserBuilder())
            ->withNetwork($network)
            ->withPointOfSale($pointOfSale)
            ->build();

        $claroPosExampleService         = ClaroServices::ClaroPos();
        $claroPosExampleService->status = ServiceStatus::ACCEPTED;

        $sale = (new SaleBuilder())
            ->withUser($salesmanCompany)
            ->withServices($claroPosExampleService)
            ->build();

        /** @var PernambucanasHook $pernambucanasHook */
        $pernambucanasHook = resolve(PernambucanasHook::class);
        $saleIsSend        = $pernambucanasHook->execute($sale->services->first());

        $this->assertTrue($saleIsSend);
    }
}
