<?php

namespace Reports\Tests\Unit\Exportables;

use Buyback\Exportables\Sales\BuybackMapSale;
use TradeAppOne\Tests\TestCase;

class BuybackMapSaleTest extends TestCase
{
    /** @test */
    public function should_have_header_and_body_the_same_length(): void
    {
        $result = BuybackMapSale::recordsToArray(BuybackExportFixture::fixture());

        self::assertEquals(count($result[0]), count($result[1]));
    }

    /** @test */
    public function should_return_one_header_and_one_record(): void
    {
        $result = BuybackMapSale::recordsToArray(BuybackExportFixture::fixture());
        self::assertEquals(2, count($result));
    }
}
