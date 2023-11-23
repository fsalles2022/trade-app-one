<?php

namespace Buyback\Exportables\Sales;

use Buyback\Exportables\AnalyticalReportIndexes;
use Buyback\Exportables\AnalyticalReportIndexes as AnalyticalIndexes;
use Buyback\Exportables\Input\TradeInSaleInlineInput;
use Buyback\Exportables\OfferDeclined\OfferDeclinedUnifiedMap;
use Buyback\Services\OfferDeclinedService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use League\Csv\Writer;
use Reports\AnalyticalsReports\BaseReportExport;
use Reports\AnalyticalsReports\Input\SalesCollectionInlineInput;
use Reports\AnalyticalsReports\Input\SalesCollectionMappableInterface;
use Reports\Criteria\DefaultAnalyticalCriteria;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\File\CsvInBulk;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Models\Collections\Sale;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Repositories\Collections\SaleReportRepository;
use TradeAppOne\Domain\Services\SaleService;
use TradeAppOne\Exceptions\BuildExceptions;

class BuybackExport extends BaseReportExport
{
    public const CHUNK_SIZE_PROCESS = 100;

    /** @inheritDoc */
    protected $filtersAvailable = [
        'startDate',
        'endDate',
        'status',
        'pointsOfSale',
        'networks',
        'operators',
        'operations',
    ];

    /** @var SaleReportRepository */
    protected $saleReportRepository;

    /** @var OfferDeclinedService */
    protected $offerDeclinedService;

    /** @var SaleService */
    private $saleService;

    public function __construct(
        SaleReportRepository $saleReportRepository,
        OfferDeclinedService $offerDeclinedService,
        SaleService $saleService
    ) {
        $this->saleReportRepository = $saleReportRepository;
        $this->offerDeclinedService = $offerDeclinedService;
        $this->saleService          = $saleService;
    }

    /**
     * @param mixed[] $filters
     * @throws BuildExceptions
     */
    public function extractAnalytical(array $filters): Writer
    {
        $this->validateFilters($filters);
        return $this->processCsvAnalytical($filters);
    }

    public function extractUnified(User $user): Writer
    {
        $offersDeclined    = $this->offerDeclinedService->getDeclinedOffersByUser($user)->get();
        $offersDeclinedMap = OfferDeclinedUnifiedMap::recordsToArray($offersDeclined);

        $salesMap = $this->getAnalytical();

        array_push($salesMap[0], AnalyticalIndexes::TYPE, AnalyticalIndexes::REASON);

        foreach ($salesMap as $index => $sale) {
            if ($index != 0) {
                $salesMap[$index] = array_merge($sale, [
                    AnalyticalIndexes::TYPE => 'VENDA',
                    AnalyticalIndexes::REASON => ''
                ]);
            }
        }

        $lines = (array_merge($salesMap, $offersDeclinedMap));
        return CsvHelper::arrayToCsv($lines);
    }

    private function getAnalytical(array $filters = []): array
    {
        $query = (new ElasticsearchQueryBuilder())
            ->where('service_sector', Operations::TRADE_IN)
            ->get();

        $filteredQuery = (new DefaultAnalyticalCriteria($filters))->apply($query);
        $analyticData  = $this->saleReportRepository->getFilteredByContextUsingScroll($filteredQuery);
        $records       =  data_get($analyticData->toArray(), 'hits.hits');

        return BuybackMapSale::recordsToArray($records);
    }

    /** @param mixed[] $filters */
    public function processCsvAnalytical(array $filters = []): Writer
    {
        $csv = new CsvInBulk();

        $csv->setSkip(0)
            ->setTake(self::CHUNK_SIZE_PROCESS)
            ->setHeader(AnalyticalReportIndexes::headings())
            ->setProcessBulkCallback(function (int $skip, int $take) use ($filters): array {
                $sales = $this->getSales($filters, $skip, $take);
                if ($sales->isEmpty()) {
                    return [];
                }
                $salesCollectionInput   = $this->mountInput($sales->all());
                $analyticalReportExport = BuybackMapSaleInline::recordsToArray($salesCollectionInput);
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
        return $this->saleService->filterAllBuybackByContext(
            Auth::user(),
            $filters,
            $skip,
            $take
        );
    }

    /**
     * @param Sale[] $sales
     * @return SalesCollectionMappableInterface
     */
    protected function mountInput(array $sales): SalesCollectionMappableInterface
    {
        $salesInput = [];

        foreach ($sales as $sale) {
            $salesInput[] = new TradeInSaleInlineInput($sale);
        }

        return new SalesCollectionInlineInput($salesInput);
    }
}
