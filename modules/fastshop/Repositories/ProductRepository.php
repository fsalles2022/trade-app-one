<?php

namespace FastShop\Repositories;

use FastShop\Models\Product;
use FastShop\Repositories\Filters\ProductRepositoryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;

class ProductRepository extends BaseRepository
{

    protected $model = Product::class;

    private function filter(Builder $queryWithContext, array $parameters): Builder
    {
        $productFilter = (new ProductRepositoryFilter($queryWithContext))->apply($parameters);
        return $productFilter->getQuery();
    }

    public function getByFilters(array $filters): Collection
    {
        $queryBuilder = $this->createModel()->newQuery();

        $query = $this->filter($queryBuilder, $filters);
        $query->orderBy('createdAt', 'desc');
        $query->groupBy(['serviceId']);
        return $query->get();
    }
}
