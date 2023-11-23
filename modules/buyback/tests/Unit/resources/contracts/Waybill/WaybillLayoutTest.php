<?php

namespace Buyback\Tests\Unit\resources\contracts\Waybill;

use Buyback\Enumerators\WaybillCarriers;
use Buyback\Resources\contracts\Waybill\WaybillLayout;
use Buyback\Tests\Helpers\Builders\WaybillBuilder;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\TestCase;

class WaybillLayoutTest extends TestCase
{
    /** @test */
    public function should_return_an_instance()
    {
        $class = new WaybillLayout((new WaybillBuilder())->build());
        
        $className = get_class($class);
        $this->assertEquals(WaybillLayout::class, $className);
    }

    /** @test */
    public function should_return_html_to_voucher()
    {
        $waybill       = (new WaybillBuilder())->build();
        $voucherLayout = new WaybillLayout($waybill);

        $html = $voucherLayout->view()->render();

        $this->assertTrue($this->isHTML($html));
        $this->assertInternalType('string', $html);
    }

    private function isHTML($text)
    {
        $processed = htmlentities($text);

        if ($processed == $text) {
            return false;
        }

        return true;
    }

    /** @test */
    public function should_return_correct_carrier_when_exist()
    {
        $waybill = (new WaybillBuilder())
            ->withOperation(Operations::SALDAO_INFORMATICA)
            ->build();

        $voucherLayout = new WaybillLayout($waybill);

        $carrier = $voucherLayout->getCarrier();

        $this->assertEquals(WaybillCarriers::WERTLOG, $carrier);
    }

    /** @test */
    public function should_return_none_when_carrier_not_exists()
    {
        $waybill = (new WaybillBuilder())
            ->withOperation(Operations::IPLACE)
            ->build();

        $voucherLayout = new WaybillLayout($waybill);
        $carrier       = $voucherLayout->getCarrier();

        $this->assertEquals('', $carrier);
    }
}
