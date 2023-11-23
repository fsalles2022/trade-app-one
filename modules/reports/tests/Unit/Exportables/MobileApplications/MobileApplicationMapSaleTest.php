<?php

namespace Reports\Tests\Unit\Exportables\MobileApplications;

use Reports\AnalyticalsReports\MobileApplications\MobileApplicationMapSale;
use TradeAppOne\Tests\TestCase;

class MobileApplicationMapSaleTest extends TestCase
{
    /** @test */
    public function should_have_header_and_body_the_same_length()
    {
        $result = MobileApplicationMapSale::recordsToArray(MobileApplicationExportFixture::fixture());
        self::assertEquals(count($result[0]), count($result[1]));
    }

    /** @test */
    public function should_return_one_header_and_one_record()
    {
        $result = MobileApplicationMapSale::recordsToArray(MobileApplicationExportFixture::fixture());
        self::assertEquals(2, count($result));
    }
}
