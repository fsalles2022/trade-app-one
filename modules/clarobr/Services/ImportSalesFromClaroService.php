<?php

namespace ClaroBR\Services;

use Carbon\Carbon;
use ClaroBR\Connection\SivConnection;
use TradeAppOne\Domain\Services\BaseService;

class ImportSalesFromClaroService extends BaseService
{
    const DATE_FORMAT                = 'Y-m-d';
    const QUANTITY_SALES_PER_REQUEST = 1000;

    protected $sivConnection;
    protected $mapper;

    public function __construct(SivConnection $sivConnection, MapSivSalesService $mapper)
    {
        $this->sivConnection = $sivConnection;
        $this->mapper        = $mapper;
    }

    public function execute(array $period)
    {
        $responseFromSiv   = $this->sivConnection->querySales($period)->toArray();
        $collectionOfSales = collect(data_get($responseFromSiv, 'data.data', []));
        $mappedSales       = $this->mapper->mapToTable($collectionOfSales);
        $recordSales       = $this->saleImportService->createMany($mappedSales);
        return $recordSales;
    }

    public function requestToDescoverTheQuantityOfPages(?Carbon $initialDate, ?Carbon $finalDate): array
    {
        $period       = [
            'de'             => isset($initialDate) ? $initialDate->format(self::DATE_FORMAT) : '2018-06-27',
            'ate'            => isset($finalDate) ? $finalDate->format(self::DATE_FORMAT) : now()->format(self::DATE_FORMAT),
            'items_per_page' => 1
        ];
        $response     = $this->sivConnection->querySales($period)->toArray();
        $totalOfPages = ceil(data_get($response, 'data.last_page') / self::QUANTITY_SALES_PER_REQUEST);
        $totalOfSales = data_get($response, 'data.total');
        return ['total' => $totalOfSales, 'pages' => $totalOfPages];
    }
}
