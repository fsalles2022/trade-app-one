<?php


namespace Generali\tests\Unit\resources;

use Generali\Models\Generali;
use Generali\resources\contracts\InsuranceTicketTemplate;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\SaleBuilder;
use TradeAppOne\Tests\TestCase;

class InsuranceTicketTermTest extends TestCase
{
    /** @test */
    public function should_return_html_insurance_ticket(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $services    = factory(Generali::class)->create();

        $sale = (new SaleBuilder())->withServices([$services])->withPointOfSale($pointOfSale)->build();

        $services->setRelation('sale', $sale);

        $layout = (new InsuranceTicketTemplate($services))->layout();
        $html   = $layout->toHtml();

        $this->assertTrue($this->isHTML($html));
        $this->assertInternalType('string', $html);
    }

    /** @test */
    public function should_return_html_with_field_of_sale(): void
    {
        $network     = (new NetworkBuilder())->build();
        $pointOfSale = (new PointOfSaleBuilder())->withNetwork($network)->build();
        $services    = factory(Generali::class)->create();

        $sale = (new SaleBuilder())->withServices([$services])->withPointOfSale($pointOfSale)->build();

        $services->setRelation('sale', $sale);

        $layout = (new InsuranceTicketTemplate($services))->layout();
        $html   = $layout->toHtml();

        $this->assertContains($layout->ticket->customer['firstName'], $html);
        $this->assertContains($layout->ticket->customer['lastName'], $html);
        $this->assertContains($layout->ticket->customer['fullAddress'], $html);
        $this->assertContains($layout->ticket->customer['neighborhood'], $html);
        $this->assertContains($layout->ticket->customer['city'], $html);
        $this->assertContains($layout->ticket->customer['state'], $html);
        $this->assertContains($layout->ticket->customer['zipCode'], $html);
        $this->assertContains('Andrea Crisanaz', $html);
    }

    private function isHTML($text): bool
    {
        return ! (htmlentities($text) == $text);
    }
}
