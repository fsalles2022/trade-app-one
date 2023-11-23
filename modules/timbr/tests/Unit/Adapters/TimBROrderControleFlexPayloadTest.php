<?php

declare(strict_types=1);

namespace TimBR\Tests\Unit\Adapters;

use TimBR\Adapters\TimBROrderControleFlexPayload;
use TimBR\Enumerators\TimBRSegments;
use TimBR\Models\TimBRControleFlex;
use TimBR\Tests\Helpers\TimFactoriesHelper;
use TradeAppOne\Domain\Models\Collections\Service;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class TimBROrderControleFlexPayloadTest extends TestCase
{
    use TimFactoriesHelper;

    public function test_should_adapted_as_information(): void
    {
        /** @var Service $service */
        $service = $this->timFactories()
            ->of(TimBRControleFlex::class)
            ->create();

        $network                          = NetworkBuilder::make()->build();
        $pointOfSale                      = PointOfSaleBuilder::make()->withNetwork($network)->build();
        $pointOfSale->providerIdentifiers = json_encode(['TIM' => 'ada']);

        $user = UserBuilder::make()->build();
        $sale = SaleBuilder::make()
            ->withServices($service)
            ->withPointOfSale($pointOfSale)
            ->withUser($user)
            ->build();

        $payloadAdapted = TimBROrderControleFlexPayload::adapt($sale->services->first());
        $order          = $payloadAdapted['order'] ?? null;

        $this->assertFalse($order['isSimulation'] ?? null);
        $this->assertNotEmpty($order['eligibilityToken']);
        $this->assertNotEmpty($order['eligibilityToken']);
        $this->assertNotEmpty($order['pdv']['custCode'] ?? null);
        $this->assertNotEmpty($order['pdv']['stateCode'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['postalCode'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['streetType'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['streetName'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['number'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['neighborhood'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['cityName'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['stateCode'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['country'] ?? null);
        $this->assertNotEmpty($order['customer']['address']['complement'] ?? null);
        $this->assertNotEmpty($order['customer']['socialSecNo'] ?? null);
        $this->assertNotEmpty($order['customer']['name'] ?? null);
        $this->assertNotEmpty($order['customer']['customerType'] ?? null);
        $this->assertNotEmpty($order['customer']['motherName'] ?? null);
        $this->assertNotEmpty($order['customer']['gender'] ?? null);
        $this->assertNotEmpty($order['customer']['birthDate'] ?? null);
        $this->assertNotEmpty($order['customer']['country'] ?? null);
        $this->assertNotEmpty($order['customer']['contactNumber'] ?? null);
        $this->assertNotEmpty($order['customer']['identityDocument'] ?? null);
        $this->assertNotEmpty($order['customer']['identityDocument']['type'] ?? null);
        $this->assertNotEmpty($order['customer']['identityDocument']['number'] ?? null);
        $this->assertNotEmpty($order['customer']['identityDocument']['issueDate'] ?? null);
        $this->assertNotEmpty($order['customer']['identityDocument']['issuerAgency'] ?? null);
        $this->assertNotEmpty($order['customer']['identityDocument']['issuerStateCode'] ?? null);
        $this->assertFalse($order['customer']['isIlliterate'] ?? null);
        $this->assertEmpty($order['customer']['disabilities'] ?? null);
        $this->assertNotEmpty($order['plan']['segment'] ?? null);
        $this->assertSame(TimBRSegments::CONTROLE_FLEX, $order['plan']['segment'] ?? null);
        $this->assertNotEmpty($order['plan']['id'] ?? null);
        $this->assertNotEmpty($order['newContract']['ddd'] ?? null);
        $this->assertNotEmpty($order['newContract']['simCard']['id'] ?? null);
        $this->assertNotEmpty($order['optin'] ?? null);
        $this->assertNotEmpty(current($order['optin'])['blockMessage'] ?? null);
        $this->assertFalse(current($order['optin'])['option'] ?? null);
        $this->assertNotEmpty($order['contractDocumentation'] ?? null);
        $this->assertNotEmpty($order['contractDocumentation']['shippingType'] ?? null);
        $this->assertNotEmpty($order['contractDocumentation']['printType'] ?? null);
        $this->assertNotEmpty($order['vendor']['nominativeVendor'] ?? null);
        $this->assertEmpty($order['witness'] ?? null);
    }
}
