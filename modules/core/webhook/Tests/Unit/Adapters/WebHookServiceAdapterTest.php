<?php

namespace Core\WebHook\Tests\Unit\Adapters;

use Core\WebHook\Adapters\WebHookServiceAdapter;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class WebHookServiceAdapterTest extends TestCase
{
    use ArrayAssertTrait;

    /** @test */
    public function should_return_correct_structure(): void
    {
        $service = factory(Service::class)->make([
            'status' => ServiceStatus::ACCEPTED
        ]);

        $sale    = SaleBuilder::make()->withServices($service)->build();
        $service = $sale->services->first();

        $received = WebHookServiceAdapter::map($service);

        $this->assertArrayStructure($received, [
            'customer',
            'status',
            'serviceTransaction',
            'salesman',
            'pointOfSale'
        ]);
    }
}
