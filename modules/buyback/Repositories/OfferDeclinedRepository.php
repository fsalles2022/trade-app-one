<?php

namespace Buyback\Repositories;

use Buyback\Models\OfferDeclined;
use TradeAppOne\Domain\Repositories\Collections\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class OfferDeclinedRepository extends BaseRepository
{
    protected $model = OfferDeclined::class;

    public function offersDeclinedByPointOfSaleIds(array $ids)
    {
        return $this->createModel()->whereIn('pointOfSale.id', $ids);
    }

    public function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $filter) {
            if (! isset($filter)) {
                continue;
            }
            switch ($key) {
                case 'cpfSalesman':
                    $query->where('user.cpf', 'like', "%$filter%");
                    break;
                case 'nameCustomer':
                    $query->where('customer.fullName', 'like', "%$filter%");
                    break;
            }
        }
        return $query;
    }
}
