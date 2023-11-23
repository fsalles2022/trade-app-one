<?php


namespace TradeAppOne\Domain\Repositories\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use TradeAppOne\Domain\Components\Helpers\MongoDateHelper;
use TradeAppOne\Domain\Models\Collections\Sale;

class SaleRepositoryFilter extends BaseFilters
{
    protected $query;

    public function __construct(Builder $query = null)
    {
        $this->query = $query ?? Sale::query();
    }

    public function name($value): SaleRepositoryFilter
    {
        $this->query->where(static function ($query) use ($value) {
            $query->where('services.customer.firstName', 'like', "%$value%");
            $query->orWhere(static function ($childQuery) use ($value) {
                $names = explode(' ', $value);
                $childQuery->whereIn('services.customer.lastName', $names);
            });
        });
        return $this;
    }

    public function salesmanCpf($value): SaleRepositoryFilter
    {
        $this->query->where('user.cpf', 'like', "%$value%");
        return $this;
    }

    public function customerCpf($value): SaleRepositoryFilter
    {
        $this->query->where('services.customer.cpf', 'like', "%$value%");
        return $this;
    }

    /** @param mixed $value */
    public function serviceName($value): SaleRepositoryFilter
    {
        $this->query->where('services.label', 'like', "%$value%");
        return $this;
    }

    /** @param mixed $value */
    public function servicePrice($value): SaleRepositoryFilter
    {
        $this->query->where('services.price', $value);
        return $this;
    }

    public function cpfCustomerWithoutLike($value): SaleRepositoryFilter
    {
        $this->query->where('services.customer.cpf', '=', $value);
        return $this;
    }

    public function saleId($value): SaleRepositoryFilter
    {
        $this->query->where('saleTransaction', 'like', "%$value%");
        return $this;
    }

    public function operator($value): SaleRepositoryFilter
    {
        $this->query->where('services.operator', $value);
        return $this;
    }

    /** @param mixed $value */
    public function operation($value): SaleRepositoryFilter
    {
        $this->query->where('services.operation', $value);
        return $this;
    }

    public function imei($value): SaleRepositoryFilter
    {
        $this->query->where('services.imei', $value);
        return $this;
    }

    public function burned($value): SaleRepositoryFilter
    {
        if ($value) {
            $this->query->whereNotNull('services.burned.current');
        } else {
            $this->query->whereNull('services.burned.current');
        }
        return $this;
    }

    public function status($value): SaleRepositoryFilter
    {
        $this->query->where('services.status', $value);
        return $this;
    }

    public function sector($value): SaleRepositoryFilter
    {
        $this->query->where('services.sector', $value);
        return $this;
    }

    public function network($value): SaleRepositoryFilter
    {
        $this->query->where('pointOfSale.network.id', $value);
        return $this;
    }

    /** @param \DateTime $value */
    public function createdAt($value): SaleRepositoryFilter
    {
        $this->query->where('createdAt', '=', MongoDateHelper::dateTimeToUtc($value));
        return $this;
    }

    public function getQuery(): Builder
    {
        return $this->query;
    }

    public function get(): Collection
    {
        return $this->query->get();
    }
}
