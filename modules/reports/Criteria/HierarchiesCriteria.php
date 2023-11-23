<?php

namespace Reports\Criteria;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;
use TradeAppOne\Domain\Models\Tables\User;
use TradeAppOne\Domain\Services\HierarchyService;

class HierarchiesCriteria implements ElasticSearchCriteria
{
    private $slugs;
    const KEY = 'CACHE_HIERARCHIES_CRITERIA';

    public function __construct(array $slugsFromHierarchies)
    {
        $this->slugs = $slugsFromHierarchies;
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        $user                  = Auth::user();
        $cnpjsFromPointsOfSale = $this->removePointsOfSaleDoNotBelongToTheUser($user);
        return (new PointsOfSalePerCnpjCriteria($cnpjsFromPointsOfSale))->apply($elasticQueryBuilder);
    }

    private function removePointsOfSaleDoNotBelongToTheUser(User $user)
    {
        $cnpjsFromPointsOfSaleByUser = Cache::get(self::KEY . $user->cpf);
        if (! $cnpjsFromPointsOfSaleByUser) {
            $hierarchyService            = resolve(HierarchyService::class);
            $userPointsOfSale            = $hierarchyService->getPointsOfSaleThatBelongsToUser($user);
            $cnpjsFromPointsOfSaleByUser = $userPointsOfSale->pluck('cnpj')->toArray();
            Cache::put(self::KEY . $user->cpf, $cnpjsFromPointsOfSaleByUser, 1440);
        }
        $cnpjsFromPointsOfSale = $this->searchForPointsOfSaleCnpjInAHierarchy();
        foreach ($cnpjsFromPointsOfSale as $index => $cnpj) {
            if (! in_array($cnpj, $cnpjsFromPointsOfSaleByUser)) {
                unset($cnpjsFromPointsOfSale[$index]);
            }
        }
        return $cnpjsFromPointsOfSale;
    }

    private function searchForPointsOfSaleCnpjInAHierarchy()
    {
        $cnpjsFromPointsOfSale = [];
        $hierarchyService      = resolve(HierarchyService::class);
        foreach ($this->slugs as $slug) {
            $hierarchy             = $hierarchyService->findOneHierarchyBySlug($slug);
            $cnpjsFromPointsOfSale = array_merge(
                $cnpjsFromPointsOfSale,
                $hierarchy->pointsOfSale->pluck('cnpj')->toArray()
            );
        }
        return $cnpjsFromPointsOfSale;
    }
}
