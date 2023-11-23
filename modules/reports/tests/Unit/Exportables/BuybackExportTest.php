<?php

namespace Reports\Tests\Unit\Exportables;

use Buyback\Exportables\AnalyticalReportIndexes;
use Buyback\Exportables\Sales\BuybackExport;
use Buyback\Tests\Helpers\Builders\OfferDeclinedBuilder;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use TradeAppOne\Tests\TestCase;

class BuybackExportTest extends TestCase
{

    /** @test */
    public function should_return_writer()
    {
        Auth::login((new UserBuilder())->build());

        $saleService = \Mockery::mock(SaleService::class)->makePartial();
        $saleService
            ->shouldReceive('filterAllBuybackByContext')
            ->andReturn(Sale::all());

        $buybackExport = app()->makeWith(BuybackExport::class, ['saleService' => $saleService]);

        $result = $buybackExport->extractAnalytical(['status' => ServiceStatus::ACCEPTED]);

        self::assertInstanceOf(Writer::class, $result);
    }

    /** @test */
    public function should_return_writer_with()
    {
        Auth::login((new UserBuilder())->build());

        $saleService = \Mockery::mock(SaleService::class)
            ->makePartial();
        $saleService
            ->shouldReceive('filterAllBuybackByContext')
            ->andReturn(Sale::all());

        $buybackExport = app()->makeWith(BuybackExport::class, ['saleService' => $saleService]);
        $result        = $buybackExport->extractAnalytical(['status' => ServiceStatus::ACCEPTED]);
        self::assertNotEmpty($result->getContent());
    }

    /** @test */
    public function should_return_writer_when_call_extractUnified()
    {
        $user = (new UserBuilder())->build();
        (new OfferDeclinedBuilder())->withUser($user)->build();

        $repository = \Mockery::mock(SaleReportRepository::class)
            ->makePartial();
        $repository
            ->shouldReceive('getFilteredByContextUsingScroll')
            ->andReturn(collect(BuybackExportFixture::fixtureFromElastic()));

        $buybackExport = app()->makeWith(BuybackExport::class, ['saleReportRepository' => $repository]);
        $result        = $buybackExport->extractUnified($user);

        foreach (AnalyticalReportIndexes::headings() as $index) {
            self::assertContains($index, $result->getContent());
        }

        self::assertContains(AnalyticalReportIndexes::TYPE, $result->getContent());
        self::assertContains(AnalyticalReportIndexes::REASON, $result->getContent());
        self::assertContains("DESISTENCIA", $result->getContent());
        self::assertContains("VENDA", $result->getContent());
    }
}
