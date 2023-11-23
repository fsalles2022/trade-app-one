<?php

namespace VivoBR\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Domain\Services\ImportSource;
use VivoBR\Connection\SunConnection;

class ImportSalesFromVivoService extends BaseService implements ImportSource
{
    const DATE_FORMAT        = 'Y-m-d-H-i';
    const INTEGRATION_SYSTEM = 'SUN';

    protected $sunConnection;
    protected $service;
    protected $mapper;

    public function __construct(SunConnection $sunConnection, MapSunSalesService $mapper)
    {
        $this->sunConnection = $sunConnection;
        $this->mapper        = $mapper;
    }

    public function execute(array $options): Collection
    {
        $resume      = collect();
        $initialDate = data_get($options, 'initialDate');
        $finalDate   = data_get($options, 'finalDate');
        $network     = data_get($options, 'network');

        if ($network) {
            $resume = $this->process($network, $initialDate, $finalDate);
        } else {
            foreach (SunConnection::HEADERS as $network => $header) {
                $result = $this->process($network, $initialDate, $finalDate);
                $resume->merge($result);
            }
        }
        return $resume;
    }

    protected function process($network, $initialDate, $finalDate): Collection
    {
        $collectionOfSales = $this->requestSource($network, $initialDate, $finalDate);
        $sales             = $this->mapper->mapToTable(self::INTEGRATION_SYSTEM, $collectionOfSales);
        return $this->saleImportService->createMany($sales);
    }

    public function requestSource(string $network, ?Carbon $initialDate, ?Carbon $finalDate): Collection
    {
        $period   = [
            'dataInicio' => isset($initialDate) ? $initialDate->format(self::DATE_FORMAT) : '2018-06-27-01-00',
            'dataFim'    => isset($finalDate) ? $finalDate->format(self::DATE_FORMAT) : now()->format(self::DATE_FORMAT)
        ];
        $response = $this->sunConnection->selectCustomConnection($network)->querySales($period)->toArray();

        return collect(data_get($response, 'vendas', []));
    }
}
