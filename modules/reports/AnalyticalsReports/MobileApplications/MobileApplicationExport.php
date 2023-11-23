<?php

namespace Reports\AnalyticalsReports\MobileApplications;

use League\Csv\Writer;
use Reports\Criteria\DefaultAnalyticalCriteria;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;

class MobileApplicationExport
{
    protected $saleReportRepository;

    public function __construct(SaleReportRepository $saleReportRepository)
    {
        $this->saleReportRepository = $saleReportRepository;
    }

    public function extractAnalytical(array $filters): Writer
    {
        $query         = (new ElasticsearchQueryBuilder())
            ->whereIn('service_operator.keyword', array_keys(Operations::MOBILE_APPS_OPERATORS))
            ->get();
        $filteredQuery = (new DefaultAnalyticalCriteria($filters))->apply($query);
        $analyticData  = $this->saleReportRepository->getFilteredByContextUsingScroll($filteredQuery);
        $records       = data_get($analyticData->toArray(), 'hits.hits');
        $lines         = MobileApplicationMapSale::recordsToArray($records);

        return CsvHelper::arrayToCsv($lines);
    }
}
