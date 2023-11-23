<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports;

use Reports\AnalyticalsReports\Input\SaleInlineInput;
use Reports\AnalyticalsReports\Input\SalesCollectionInlineInput;
use Reports\AnalyticalsReports\Input\SalesCollectionMappableInterface;
use Reports\Exceptions\ReportExceptions;
use TradeAppOne\Domain\Models\Collections\Sale;

abstract class BaseReportExport
{
    /** @var string[] */
    protected $filtersAvailable = [];

    /** @param mixed[] $filters */
    protected function validateFilters(array $filters): void
    {
        $keys = array_keys($filters);

        foreach ($keys as $key) {
            if (in_array($key, $this->filtersAvailable, true)) {
                return;
            }
        }

        throw ReportExceptions::filterRequestedReportBuild();
    }

    /**
     * @param Sale[] $sales
     * @return SalesCollectionMappableInterface
     */
    protected function mountInput(array $sales): SalesCollectionMappableInterface
    {
        $salesInput = [];

        foreach ($sales as $sale) {
            $salesInput[] = new SaleInlineInput($sale);
        }

        return new SalesCollectionInlineInput($salesInput);
    }
}
