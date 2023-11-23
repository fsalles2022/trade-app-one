<?php

declare(strict_types=1);

namespace Reports\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;
use Reports\AnalyticalsReports\BaseReportExport;
use TradeAppOne\Domain\Components\File\CsvInBulk;
use TradeAppOne\Domain\Exportables\AnalyticalReportExport;
use TradeAppOne\Domain\Services\SaleService;

class AnalyticalReportService extends BaseReportExport
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
            ->setHeader(AnalyticalReportExport::headings())
            ->setProcessBulkCallback(function (int $skip, int $take) use ($filters): array {
                $sales = $this->getSales($filters, $skip, $take);

                if ($sales->isEmpty()) {
                    return [];
                }

                $salesCollectionInput = $this->mountInput($sales->all());

                $analyticalReportExport = AnalyticalReportExport::recordsToArray($salesCollectionInput);

                unset($analyticalReportExport[0]);

                return $analyticalReportExport;
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
        return $this->saleService->filterAllActivationByContext(
            Auth::user(),
            $filters,
            $skip,
            $take
        );
    }
}
