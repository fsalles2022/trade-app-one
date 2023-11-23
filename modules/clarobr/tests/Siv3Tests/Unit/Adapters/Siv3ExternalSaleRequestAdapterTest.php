<?php

declare(strict_types=1);

namespace ClaroBR\Tests\Siv3Tests\Unit\Adapters;

use ClaroBR\Adapters\Siv3ExternalSaleRequestAdapter;
use TradeAppOne\Domain\Enumerators\Modes;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class Siv3ExternalSaleRequestAdapterTest extends TestCase
{
    /** @return array */
    public function external_sales_from_siv3(): array
    {
        return [
            [
                [
                    'mode' => Modes::ACTIVATION,
                    'areaCode' => '11',
                    'msisdn' => '1192986-7070',
                    'iccid' => '89550000000000000000',
                    'customerCpf' => '952.553.108-24',
                    'salesmanCpf' => '251.771.228-08',
                    'pointOfSaleCode' => 'XPTO',
                    'networkSlug' => 'tradeup-group'
                ],
                [
                    'mode' => Modes::MIGRATION,
                    'areaCode' => '12',
                    'msisdn' => '1192996-7070',
                    'iccid' => '89550001230000000000',
                    'customerCpf' => '951.551.108-21',
                    'salesmanCpf' => '252.772.222-02',
                    'pointOfSaleCode' => 'VT3',
                    'networkSlug' => 'via-varejo'
                ]
            ]
        ];
    }

    /**
     * @dataProvider external_sales_from_siv3
     * @param string[] $sales
     * @test
     */
    public function method_should_returned_array(array $sales): void
    {
        $netWorkEntity = (new NetworkBuilder())->build();
        $pointOfSale   = (new PointOfSaleBuilder())->withNetwork($netWorkEntity)->build();
        $userHelper    = (new UserBuilder())->withPointOfSale($pointOfSale)->build();

        $arrayOfSales = resolve(Siv3ExternalSaleRequestAdapter::class)->adapt($sales, $userHelper);

        $this->assertNotEmpty(array_values($arrayOfSales));
        $this->assertArrayHasKey('mode', $arrayOfSales);
        $this->assertArrayHasKey('areaCode', $arrayOfSales);
        $this->assertArrayHasKey('msisdn', $arrayOfSales);
        $this->assertArrayHasKey('iccid', $arrayOfSales);
        $this->assertArrayHasKey('customerCpf', $arrayOfSales);
        $this->assertArrayHasKey('salesmanCpf', $arrayOfSales);
        $this->assertArrayHasKey('pointOfSaleCode', $arrayOfSales);
        $this->assertArrayHasKey('networkSlug', $arrayOfSales);
    }
}
