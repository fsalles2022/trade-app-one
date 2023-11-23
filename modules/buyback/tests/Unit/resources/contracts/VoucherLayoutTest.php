<?php

namespace Buyback\Tests\Unit\resources\contracts;

use Buyback\Resources\contracts\VoucherLayout;
use Buyback\Tests\Helpers\TradeInServices;
use Jenssegers\Date\Date;
use TradeAppOne\Domain\Components\Helpers\FormatHelper;
use TradeAppOne\Domain\Components\Helpers\MsisdnHelper;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class VoucherLayoutTest extends TestCase
{
    use AuthHelper;

    protected $endpointSale = 'sales';

    /** @test */
    public function should_return_html_to_voucher()
    {
        $servicePrototype = TradeInServices::SaldaoInformaticaMobile();
        $sale             = (new SaleBuilder())->withServices([$servicePrototype])->build();
        $voucherLayout    = new VoucherLayout($sale->services()->first()->toArray(), $sale);

        $html = $voucherLayout->toHtml();

        $this->assertTrue($this->isHTML($html));
        $this->assertInternalType('string', $html);
    }

    /** @test */
    public function should_return_html_with_informations_from_sale()
    {
        $servicePrototype = TradeInServices::SaldaoInformaticaMobile();
        $sale             = (new SaleBuilder())->withServices([$servicePrototype])->build();
        $voucherLayout    = new VoucherLayout($sale->services()->first()->toArray(), $sale);

        $html    = $voucherLayout->toHtml();
        $service = $sale->services()->first();

        $customer     = $service->customer;
        $fullName     = $customer['firstName'] . " " . $customer['lastName'];
        $local        = $customer['local'];
        $number       = $customer['number'];
        $neighborhood = $customer['neighborhood'];
        $customerCity = $customer['city'];
        $state        = $customer['state'];
        $cpf          = FormatHelper::mask($customer['cpf'], '###.###.###-##');
        $email        = $customer['email'];
        $mainPhone    = MsisdnHelper::removeCountryCode(MsisdnHelper::BR, $customer['mainPhone']);
        $mainPhone    = FormatHelper::mask($mainPhone, '(##) #####-#####');
        $zipCode      = FormatHelper::mask($customer['zipCode'], '#####-###');


        $deviceModel = $service->device['model'];
        $deviceBrand = $service->device['brand'];
        $deviceColor = $service->device['color'];
        $storage     = $service->device['storage'];

        $serviceTransaction = $service->serviceTransaction;

        $city  = ucwords(mb_strtolower($sale->pointOfSale['city']));
        $date  = (new Date($sale->updatedAt))->format('d \d\e F \d\e Y');
        $price = $service['price'];

        $this->assertContains($serviceTransaction, $html);
        $this->assertContains($fullName, $html);
        $this->assertContains($local, $html);
        $this->assertContains($number, $html);
        $this->assertContains($neighborhood, $html);
        $this->assertContains($customerCity, $html);
        $this->assertContains($state, $html);
        $this->assertContains($cpf, $html);
        $this->assertContains($email, $html);
        $this->assertContains($mainPhone, $html);
        $this->assertContains($city, $html);
        $this->assertContains($date, $html);
        $this->assertContains($deviceModel, $html);
        $this->assertContains($deviceBrand, $html);
        $this->assertContains($deviceColor, $html);
        $this->assertContains(strval($price), $html);
        $this->assertContains($zipCode, $html);
        $this->assertContains($storage, $html);
    }

    private function isHTML($text)
    {
        $processed = htmlentities($text);

        return ! ($processed == $text);
    }
}
