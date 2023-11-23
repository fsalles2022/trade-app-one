<?php

namespace ClaroBR\Services;

use ClaroBR\Adapters\MapSivSale;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\BaseService;
use TradeAppOne\Exceptions\BusinessExceptions\PointOfSaleNotFoundException;
use TradeAppOne\Exceptions\BusinessExceptions\UserNotFoundException;

class MapSivSalesService extends BaseService
{
    const WEB = 'WEB';
    const APP = 'APP';

    public function mapToTable(Collection $salesFromSiv): Collection
    {
        $sales        = new Collection();
        $onlyWebSales = $this->getOnlyWebAndAppSales($salesFromSiv);
        foreach ($onlyWebSales as $saleFromSiv) {
            $sale  = $this->mapRow($saleFromSiv);
            $sales = $sales->merge($sale);
        }
        return $sales;
    }

    private function getOnlyWebAndAppSales(Collection $salesFromSiv): Collection
    {
        return $salesFromSiv->whereIn('client', [self::WEB, self::APP]);
    }

    public function mapRow(array $saleFromSiv)
    {
        $pointOfSaleCnpj     = data_get($saleFromSiv, 'pos.cnpj');
        $pointOfSaleCodClaro = data_get($saleFromSiv, 'pos.codigo');
        $userCpf             = data_get($saleFromSiv, 'user.cpf');

        try {
            if ($pointOfSaleCnpj) {
                $pointOfSaleEntity = $this->pointOfSaleService->findOneByCnpj($pointOfSaleCnpj);
            } else {
                $pointOfSaleEntity = $this->pointOfSaleService->findOneByProviderIdentifiers(Operations::CLARO, $pointOfSaleCodClaro);
            }
        } catch (PointOfSaleNotFoundException $exception) {
            return [];
        }

        try {
            $userEntity = $this->userService->findOneByCpf($userCpf);
        } catch (UserNotFoundException $exception) {
            $userEntity = null;
        }

        return MapSivSale::mapOne($pointOfSaleEntity, $userEntity, $saleFromSiv);
    }
}
