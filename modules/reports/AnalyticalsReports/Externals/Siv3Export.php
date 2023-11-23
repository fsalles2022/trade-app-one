<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports\Externals;

use ClaroBR\Adapters\Siv3ReportExternalSaleResponseAdapter;
use ClaroBR\Connection\Siv3Connection;
use ClaroBR\Reports\Adapters\ExternalSalesMap;
use League\Csv\Writer;
use Reports\AnalyticalsReports\BaseReportExport;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;

class Siv3Export extends BaseReportExport
{
    /** @var Siv3Connection */
    private $siv3Connection;

    /** @var string[] */
    protected $filtersAvailable = [
        'networks',
        'status',
        'startDate',
        'endDate'
    ];

    public function __construct(Siv3Connection $siv3Connection)
    {
        $this->siv3Connection = $siv3Connection;
    }

    /**
     * @param mixed[] $attributes
     * @return Writer
     */
    public function getExternalSales(array $attributes): Writer
    {
        $sales = new Siv3ReportExternalSaleResponseAdapter($this->siv3Connection->getSalesToReport($attributes));
        $lines = ExternalSalesMap::recordsToArray($sales->getAdapted());

        return CsvHelper::arrayToCsv($lines);
    }
}
