<?php

namespace TradeAppOne\Tests\Unit\Domain\Services\NetworkHooks;

use McAfee\Models\McAfeeMobileSecurity;
use McAfee\Tests\Helpers\McAfeeFactoriesHelper;
use Outsourced\Cea\Hooks\CeaHooks;
use Outsourced\Cea\Models\CeaGiftCard;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHookJob;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class NetworkHookJobTest extends TestCase
{
    use McAfeeFactoriesHelper;

    /** @test */
    public function should_execute_job()
    {
//        $service        = $this->buildSaleCea();
//        $card           = factory(CeaGiftCard::class)->create();
//        $saleRepository = resolve(SaleRepository::class);
//
//        $job = new NetworkHookJob(CeaHooks::class, $service->serviceTransaction);
//
//        $job->handle($saleRepository);
//
//        $this->assertDatabaseHas('sales', [
//            'services.serviceTransaction' => $service->serviceTransaction,
//            'services.register.card' => (string)$card->code
//        ], 'mongodb');
    }

//    private function buildSaleCea(): Service
//    {
//        $service = $this->mcAfeeFactories()
//            ->of(McAfeeMobileSecurity::class)
//            ->make([
//                'status' => ServiceStatus::APPROVED,
//                'operator' => Operations::TRADE_IN_MOBILE
//            ]);
//
//        $sale = (new SaleBuilder())->withServices([$service])->build();
//        return $sale->services->first();
//    }
}