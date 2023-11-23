<?php

namespace Buyback\Tests\Unit\resources\contracts\Vouchers;

use Buyback\Resources\contracts\Vouchers\Iplace\VoucherIplaceLayout;
use Buyback\Tests\Helpers\TradeInServices;
use Jenssegers\Date\Date;
use TradeAppOne\Domain\Components\Helpers\FormatHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class VoucherIplaceLayoutTest extends TestCase
{
    /** @test */
    public function should_return_html_with_informations_from_sale_iplace()
    {
        $servicePrototype = TradeInServices::IplaceMobile();
        $sale             = (new SaleBuilder())->withServices([$servicePrototype])->build();
        $service          = $sale->services()->first();

        $voucherLayout = new VoucherIplaceLayout($service->toArray(), $service->sale);
        $html          = $voucherLayout->toHtml();

        $device   = $service->device;
        $customer = $service->customer;

        $city     = ucwords(mb_strtolower($sale->pointOfSale['city']));
        $date     = (new Date($sale->updatedAt))->format('d \d\e F \d\e Y');
        $fullName = $customer['firstName'] . " " . $customer['lastName'];
        $cpf      = FormatHelper::mask($customer['cpf'], '###.###.###-##');

        $this->assertContains($city, $html);
        $this->assertContains($date, $html);
        $this->assertContains($fullName, $html);
        $this->assertContains($cpf, $html);
        $this->assertContains($device['brand'], $html);
        $this->assertContains($device[ 'model'], $html);
        $this->assertContains($device['color'], $html);
        $this->assertContains($device['storage'], $html);
        $this->assertContains($device['imei'], $html);

        $this->assertFileExists($voucherLayout->getPath());
        $this->assertContains($voucherLayout->getPath(), $html);
    }
}
