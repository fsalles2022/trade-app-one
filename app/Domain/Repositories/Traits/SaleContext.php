<?php

namespace TradeAppOne\Domain\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use TradeAppOne\Domain\Enumerators\ContextEnum;
use TradeAppOne\Domain\Enumerators\Permissions\SalePermission;

trait SaleContext
{
    public function filterByContext(): Builder
    {
        $user         = Auth::user();
        $queryBuilder = $this->createModel()->newQuery();

        $scope = $user->getUserContext(SalePermission::NAME);

        $pointsOfSaleCollection = $this->hierarchyRepository->getPointsOfSaleThatBelongsToUser($user);
        $cnpjs                  = $pointsOfSaleCollection->pluck('cnpj')->toArray();

        if ($scope == ContextEnum::CONTEXT_NON_EXISTENT) {
            return $queryBuilder->where('user.cpf', $user->cpf)->whereIn('pointOfSale.cnpj', $cnpjs);
        }

        return $queryBuilder->whereIn('pointOfSale.cnpj', $cnpjs);
    }
}
