<?php


namespace Buyback\Tests\Unit\resources\contracts\Vouchers;

use Buyback\Resources\contracts\Vouchers\Cea\VoucherCeaLayout;
use Buyback\Tests\Helpers\TradeInServices;
use Picqer\Barcode\BarcodeGeneratorPNG;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class VoucherCeaLayoutTest extends TestCase
{
    /** @test */
    public function should_return_html_with_info_thirdParty_cea()
    {
        $network     = factory(Network::class)->create(['slug' => NetworkEnum::CEA]);
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();

        $giftCard      = sprintf("%09d", random_int(0, 999999999));
        $service       = TradeInServices::TradeNetMobile(['register' => ['card' => $giftCard]]);
        $sale          = (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();
        $voucherLayout = new VoucherCeaLayout($sale->services()->first()->toArray(), $sale);

        $html      = $voucherLayout->toHtml();
        $generator = new BarcodeGeneratorPNG();

        $this->assertContains(trans('buyback::messages.voucher_thirdParty_cea_title'), $html);
        $this->assertContains(base64_encode($generator->getBarcode($giftCard, $generator::TYPE_CODABAR)), $html);
    }

    /** @test */
    public function should_return_html_without_info_thirdParty()
    {
        $network     = factory(Network::class)->create();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();

        $giftCard      = uniqid("", true);
        $service       = TradeInServices::TradeNetMobile(['register' => ['card' => $giftCard]]);
        $sale          = (new SaleBuilder())->withServices([$service])->withPointOfSale($pointOfSale)->build();
        $voucherLayout = new VoucherCeaLayout($sale->services()->first()->toArray(), $sale);

        $html = $voucherLayout->toHtml();

        $this->assertNotContains(trans('buyback::messages.voucher_thirdParty_cea_title'), $html);
        $this->assertNotContains($giftCard, $html);
    }
}
