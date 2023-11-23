<?php


namespace FastShop\Repositories\Filters;

use FastShop\Enumerators\OperationPlans;
use FastShop\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Repositories\Filters\BaseFilters;

class ProductRepositoryFilter extends BaseFilters
{
    protected $query;

    public function __construct(Builder $query = null)
    {
        $this->query = $query ?? Product::query();
    }

    public function tipo($value): ProductRepositoryFilter
    {
        $services = OperationPlans::PLANS_MAP[$value] ?? null;
        if ($services !== null) {
            $this->query->whereHas('service', static function ($service) use ($services) {
                return $service->whereIn('operation', $services);
            });
        }
        return $this;
    }

    public function ddd($value): ProductRepositoryFilter
    {
        $this->query->where('areaCode', $value);
        return $this;
    }

    public function internet($value): ProductRepositoryFilter
    {
        $value /= 1000;
        $this->query->where('internet', '>=', $value);
        return $this;
    }

    public function operadoraAtual($value): ProductRepositoryFilter
    {
        $this->query->whereHas('service', static function ($services) use ($value) {
            return $services->where('operator', $value);
        });
        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }
}
