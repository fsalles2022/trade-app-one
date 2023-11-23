<?php

namespace Reports\Services;

use Illuminate\Support\Facades\Cache;
use Reports\Adapters\MergePointsOfSaleWithNetworksFilter;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Policies\Authorizations;

class FiltersService extends Authorizations
{
    public const CACHE_FILTERS = 'CACHE_FILTERS';

    public function getFilters(User $user)
    {
        $this->setUser($user);

        if ($filters = Cache::get(self::CACHE_FILTERS . $user->cpf)) {
            return $filters;
        }

        $networks    = $this->getNetworksAuthorized();
        $hierarchies = $this->getHierarchiesAuthorized();
        $pointOfSale = $this->getPointsOfSaleAuthorized();

        $filters = (new MergePointsOfSaleWithNetworksFilter($networks, $hierarchies, $pointOfSale))->adapt();
        Cache::put(self::CACHE_FILTERS . $user->cpf, $filters, 60);

        return $filters;
    }
}
