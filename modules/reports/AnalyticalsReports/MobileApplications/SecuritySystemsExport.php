<?php

declare(strict_types=1);

namespace Reports\AnalyticalsReports\MobileApplications;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;
use Reports\AnalyticalsReports\BaseReportExport;
use TradeAppOne\Domain\Components\File\CsvInBulk;
use TradeAppOne\Domain\Services\SaleService;

class SecuritySystemsExport extends BaseReportExport
{
    public const CHUNK_SIZE_PROCESS = 1000;

    /** @var SaleService */
    protected $saleService;

    /** @var string[] */
    protected $filtersAvailable = [
        'status',
        'pointsOfSale',
        'operators',
        'operations',
        'startDate',
        'endDate',
    ];

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    /**
     * @param mixed[] $filters
     * @return Writter
     */
    public function extractAnalytical(array $filters): Writer
    {
        $this->validateFilters($filters);

        return $this->processCsv($filters);
    }

    /**
     * @param mixed[] $filters
     * @return Writter
     */
    private function processCsv(array $filters): Writer
    {
        $csv = new CsvInBulk();

        $csv->setSkip(0)
            ->setTake(self::CHUNK_SIZE_PROCESS)
            ->setHeader(SecuritySystemMapSale::headings())
            ->setProcessBulkCallback(function (int $skip, int $take) use ($filters): array {
                $sales = $this->getSales($filters, $skip, $take);

                if ($sales->isEmpty()) {
                    return [];
                }

                $salesCollectionInput = $this->mountInput($sales->all());

                $securityReportExport = SecuritySystemMapSale::recordsToArray($salesCollectionInput);

                unset($securityReportExport[0]);

                return $securityReportExport;
            });

        return $csv->build();
    }

    /**
     * @param mixed[] $filters
     * @param int|null $skip
     * @param int|null $take
     * @return Collection
     */
    private function getSales(
        array $filters,
        int $skip,
        int $take
    ): Collection {
        return $this->saleService->filterAllSecuritySystemsByContext(
            Auth::user(),
            $filters,
            $skip,
            $take
        );
    }
}
