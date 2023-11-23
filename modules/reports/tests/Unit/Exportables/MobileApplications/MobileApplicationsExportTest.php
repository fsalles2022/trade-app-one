<?php

namespace Reports\Tests\Unit\Exportables\MobileApplications;

use League\Csv\Writer;
use Reports\AnalyticalsReports\MobileApplications\MobileApplicationExport;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Tests\TestCase;

class MobileApplicationsExportTest extends TestCase
{
    /** @test */
    public function should_return_writer()
    {
        $repository = \Mockery::mock(SaleReportRepository::class)->makePartial();
        $repository
            ->shouldReceive('getFilteredByContextUsingScroll')
            ->andReturn(collect(MobileApplicationExportFixture::fixtureFromElastic()));

        $buybackExport = new MobileApplicationExport($repository);
        $result        = $buybackExport->extractAnalytical([]);

        self::assertInstanceOf(Writer::class, $result);
    }

    /** @test */
    public function should_return_writer_with()
    {
        $repository = \Mockery::mock(SaleReportRepository::class)
            ->makePartial();
        $repository
            ->shouldReceive('getFilteredByContextUsingScroll')
            ->andReturn(collect(MobileApplicationExportFixture::fixtureFromElastic()));

        $buybackExport = new MobileApplicationExport($repository);
        $result        = $buybackExport->extractAnalytical([]);
        self::assertNotEmpty($result->getContent());
    }
}
