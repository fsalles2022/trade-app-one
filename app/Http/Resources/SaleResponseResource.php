<?php

namespace TradeAppOne\Http\Resources;

use Illuminate\Pagination\LengthAwarePaginator;

class SaleResponseResource
{
    public static function adapt(LengthAwarePaginator $paginator)
    {
        if ($imei = request()->get('imei')) {
            $sale = $paginator->getCollection()->first();
            if ($sale) {
                $services            = data_get($sale, 'services');
                $servicesFiltered    = $services->where('imei', $imei)->values();
                $newSale             = $sale->toArray();
                $newSale['services'] = $servicesFiltered;
                $paginator->setCollection(collect([$newSale]));
                return $paginator;
            }
        }
        return $paginator;
    }
}
