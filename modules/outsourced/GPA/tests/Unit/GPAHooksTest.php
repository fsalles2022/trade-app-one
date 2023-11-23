<?php


namespace Outsourced\GPA\tests\Unit;

use Outsourced\GPA\tests\GPATestHelpers;
use Outsourced\GPA\tests\Helpers\GPATestBook;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Services\NetworkHooks\NetworkHooksFactory;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class GPAHooksTest extends TestCase
{
    /** @test */
    public function should_persist_triangulation_sale_and_update_services_with_retrySend_when_status_not_created(): void
    {
        $attributes                   = GPATestHelpers::createObject();
        $structure                    = GPATestHelpers::service();
        $structure['status']          = ServiceStatus::APPROVED;
        $structure['customer']['cpf'] = GPATestBook::FAILURE_CUSTOMER;

        $sale = (new SaleBuilder())
            ->withPointOfSale($attributes->pointOfSale)
            ->withUser($attributes->user)
            ->withServices([factory(Service::class)->create($structure)])
            ->build();

        $service = $sale->services->first();
        NetworkHooksFactory::run($service);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $service->serviceTransaction,
            'services.retrySend' => true
        ], 'mongodb');
    }

    /** @test */
    public function should_persist_triangulation_sale_and_send_payload_success(): void
    {
        $attributes          = GPATestHelpers::createObject();
        $structure           = GPATestHelpers::service();
        $structure['status'] = ServiceStatus::APPROVED;

        $sale = (new SaleBuilder())
            ->withPointOfSale($attributes->pointOfSale)
            ->withUser($attributes->user)
            ->withServices([factory(Service::class)->create($structure)])
            ->build();

        $service = $sale->services->first();
        NetworkHooksFactory::run($service);

        $this->assertDatabaseHas('sales', [
            'services.serviceTransaction' => $service->serviceTransaction,
        ], 'mongodb');
    }
}
