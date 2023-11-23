<?php

declare(strict_types=1);

namespace Buyback\Tests\Unit\Console;

use Illuminate\Support\Facades\Artisan;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Domain\Repositories\Collections\SaleRepository;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class TradeInCancelServiceCommandTest extends TestCase
{
    protected const SIGNATURE = 'voucher:cancel';

    public function test_command_trade_in_cancel_service_command_exists(): void
    {
        $this->assertArrayHasKey(self::SIGNATURE, Artisan::all());
    }

    public function test_should_cancel_trade_in(): void
    {
        $service = factory(Service::class)->create([
            'operator' => Operations::TRADE_IN_MOBILE,
            'status' => ServiceStatus::ACCEPTED
        ]);

        $sale    = SaleBuilder::make()->withServices($service)->build();
        $service = $sale->services()->first();

        Artisan::call(self::SIGNATURE);

        /** @var Sale $saleUpdated */
        $saleUpdated    = resolve(SaleRepository::class)->find($sale->saleTransaction);
        $serviceUpdated = $saleUpdated->services()->first();

        $this->assertFalse($serviceUpdated->status === $service->status);
        $this->assertEquals(ServiceStatus::CANCELED, $serviceUpdated->status);

        $this->assertEquals(
            [
                [
                    'serviceTransaction' => $service->serviceTransaction,
                    'status' => ServiceStatus::CANCELED,
                    'date' => $serviceUpdated->log[0]['date'] ?? null
                ]
            ],
            $serviceUpdated->log
        );
    }
}
