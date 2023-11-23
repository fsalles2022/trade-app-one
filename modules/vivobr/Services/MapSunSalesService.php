<?php

namespace VivoBR\Services;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Domain\Services\SaleMapeable;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;
use VivoBR\Adapters\MapSale;
use VivoBR\Enumerators\SunStatus;

class MapSunSalesService extends BaseService implements SaleMapeable
{
    const API = 'API';
    const WEB = 'WEB';
    const APK = 'APK';

    public function mapToTable(string $source, Collection $salesFromSun): Collection
    {
        $sales             = [];
        $salesNotMadeByTAO = $this->removeSalesMadeByTAO($salesFromSun);
        foreach ($salesNotMadeByTAO as $saleFromSun) {
            $sale  = $this->mapRow($source, $saleFromSun);
            $sales = array_merge($sales, $sale);
        }
        return collect($sales);
    }

    private function removeSalesMadeByTAO(Collection $salesFromSun): Collection
    {
        return $salesFromSun->where('origem', '!=', self::API);
    }

    public function mapRow($source, array $saleFromSun)
    {
        $pointOfSaleCnpj = data_get($saleFromSun, 'cnpjPdv');
        $userCpf         = data_get($saleFromSun, 'cpfVendedor');
        try {
            $pointOfSaleEntity = $this->pointOfSaleService->findOneByCnpj($pointOfSaleCnpj);
        } catch (PointOfSaleNotFoundException $exception) {
            $pointOfSaleEntity = null;
        }
        try {
            $userEntity = $this->userService->findOneByCpf($userCpf);
        } catch (UserNotFoundException $exception) {
            $userEntity = null;
        }
        return MapSale::mapOne($pointOfSaleEntity, $userEntity, $source, $saleFromSun);
    }

    public function mapAttributesToMongo(array $sale): array
    {
        $status       = $sale['servicos'][0]['status'] ?? '-';
        $observations = isset($sale['observacoes']) ? collect($sale['observacoes']) : collect();

        $observations = $observations->map(static function (array $obs) {
            return array_filter([
                'id' => data_get($obs, 'id'),
                'reason' => data_get($obs, 'motivo'),
                'source' => data_get($obs, 'origem'),
                'dateTime' => data_get($obs, 'dataHora'),
                'observation' => data_get($obs, 'observacao'),
            ]);
        })->toArray();

        return array_filter([
            'statusThirdParty' => $status,
            'status' => data_get(SunStatus::ORIGINAL_STATUS, $status),
            'observations' => $observations,
        ]);
    }
}
