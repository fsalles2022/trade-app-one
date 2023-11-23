<?php

namespace TradeAppOne\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;

class HealthSalesReport extends Command
{

    protected $signature   = 'health:salesreport';
    protected $description = 'Measure Sales Report Health';

    const SALE_REPORT_TODAY_SALES = 'SALE_REPORT_TODAY_SALES';

    public function __construct(ElasticConnection $saleReportRepository)
    {
        parent::__construct();
        $this->elasticConnection = $saleReportRepository;
    }

    public function handle()
    {
        $elasticSearchArray = $this->elasticConnection->execute($this->getQuery());
        $totalSales         = data_get($elasticSearchArray, 'hits.total', 0);

        $lastValue = Cache::get(self::SALE_REPORT_TODAY_SALES);
        Cache::put(self::SALE_REPORT_TODAY_SALES, $totalSales, 900);

        if ($this->salesDidntIncrease($totalSales, $lastValue)) {
            Log::alert('sales_report_stop_sync');
        }
    }

    private function getQuery()
    {
        $aggsOperatorPrices = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price');

        $aggsOperator = (new ElasticsearchAggregationStructure('operator'))
            ->terms('service_operator.keyword')
            ->nest($aggsOperatorPrices);

        $aggsTotalPrice = (new ElasticsearchAggregationStructure('sum_price'))
            ->sum('service_price')
            ->brother($aggsOperator);

        return (new ElasticsearchQueryBuilder)
            ->whereIn('service_status', GroupOfStatus::VALID_SALES)
            ->size(0)
            ->aggregations($aggsTotalPrice)
            ->get();
    }

    /**
     * @param $totalSales
     * @param $lastValue
     * @return bool
     */
    protected function salesDidntIncrease($totalSales, $lastValue): bool
    {
        return $totalSales <= $lastValue;
    }
}
