<?php

namespace TradeAppOne\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mockery;
use TradeAppOne\Console\Commands\HealthSalesReport;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchConnection;
use TradeAppOne\Tests\TestCase;

class HealthSalesReportTest extends TestCase
{
    const COMMAND = 'health:salesreport';

    public function should_be_callable()
    {
        Artisan::call(self::COMMAND);
    }

    /** @test */
    public function should_thrown_exception_when_sales_report_dont_grow()
    {
        $totalValue = 1;
        $cacheReturn = 1;
        $this->bindHealthSales($totalValue, $cacheReturn);

        Log::shouldReceive('alert')->once();
        Artisan::call(self::COMMAND);
    }

    /** @test */
    public function should_thrown_exception_when_sales_report_is_empty()
    {
        $totalValue = null;

        $cacheReturn = 1;
        $this->bindHealthSales($totalValue, $cacheReturn);

        Log::shouldReceive('alert')->once();
        Artisan::call(self::COMMAND);
    }

    /** @test */
    public function should_not_thrown_exception_when_sales_report_grow()
    {
        $totalValue = 2;
        $cacheReturn = 1;

        $this->bindHealthSales($totalValue, $cacheReturn);

        Artisan::call(self::COMMAND);
    }

    /** @test */
    public function should_update_cache_with_total_sales_when_is_empty()
    {
        $totalValue = 8;
        $cacheReturn = null;
        $this->bindHealthSales($totalValue, $cacheReturn);

        Cache::shouldReceive('put')->with(HealthSalesReport::SALE_REPORT_TODAY_SALES, $totalValue);
        Artisan::call(self::COMMAND);
    }

    /**
     * @param $collectionReturned
     * @param $cacheReturn
     */
    protected function bindHealthSales($totalValue, $cacheReturn): void
    {
        $collectionReturned = ['hits' => ['total' => $totalValue]];
        $elasticConnection = Mockery::mock(ElasticsearchConnection::class)->makePartial();
        $elasticConnection->shouldReceive('execute')->once()->andReturn($collectionReturned);

        Cache::shouldReceive('get')->andReturn($cacheReturn);
        Cache::shouldReceive('put')->with(HealthSalesReport::SALE_REPORT_TODAY_SALES, $totalValue, 900);

        app()->singleton(HealthSalesReport::class, function () use ($elasticConnection) {
            return new HealthSalesReport($elasticConnection);
        });
    }
}
