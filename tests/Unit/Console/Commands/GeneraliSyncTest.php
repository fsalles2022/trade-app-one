<?php


namespace TradeAppOne\Tests\Unit\Console\Commands;

use Generali\Assistance\Connection\GeneraliConnection;
use Generali\Console\Commands\GeneraliSentinel;
use Generali\Models\Generali;
use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class GeneraliSyncTest extends TestCase
{
    /** @test */
    public function should_update_status_and_log_from_generali_server(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $services    = factory(Generali::class)->create();
        $sale        = (new SaleBuilder())->withServices([$services])->withPointOfSale($pointOfSale)->build();

        $services->status = ServiceStatus::PENDING_SUBMISSION;

        $services->setRelation('sale', $sale);

        Artisan::call('generali:sentinel');

        $serviceUpdated = resolve(SaleService::class)->findService($services->serviceTransaction);

        $this->assertNotEquals($services->status, $serviceUpdated->status);
        $this->assertEquals(ServiceStatus::SUBMITTED, $serviceUpdated->status);
        $this->assertEquals(ServiceStatus::PENDING_SUBMISSION, $services->status);
    }
}