<?php

namespace Buyback\Tests\Unit\Services;

use Buyback\Resources\contracts\Vouchers\Iplace\VoucherIplaceLayout;
use Buyback\Services\TradeInIplaceService;
use Buyback\Tests\Helpers\TradeInServices;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class TradeInIplaceServiceTest extends TestCase
{
    /** @test */
    public function should_return_voucher_iplace()
    {
        $servicePrototype = TradeInServices::IplaceMobile();
        $sale             = (new SaleBuilder())->withServices([$servicePrototype])->build();
        $service          = $sale->services()->first();

        $serviceIplace = resolve(TradeInIplaceService::class);
        $voucherLayout = $serviceIplace->produceVoucherIplace($service);

        $this->assertInstanceOf(VoucherIplaceLayout::class, $voucherLayout);
    }
}
