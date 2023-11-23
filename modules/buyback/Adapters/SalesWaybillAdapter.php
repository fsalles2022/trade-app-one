<?php

namespace Buyback\Adapters;

use Buyback\Services\Waybill;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use TradeAppOne\Domain\Enumerators\ServiceStatus;

class SalesWaybillAdapter
{
    public static function adapter(Collection $sales, Collection $pointsOfSale, array $operations): Collection
    {
        $waybills = collect();

        foreach ($pointsOfSale as $pointOfSale) {
            $salesPointOfSale = $sales->where('pointOfSale.cnpj', '=', $pointOfSale->cnpj);

            $services = collect();

            foreach ($salesPointOfSale as $sale) {
                $servicesFiltered = $sale->services
                    ->whereIn('operation', $operations)
                    ->where('status', ServiceStatus::ACCEPTED)
                    ->where('waybill.printedAt', null);

                $services = $services->merge($servicesFiltered);
            }

            if ($services->isNotEmpty()) {
                foreach ($operations as $operation) {
                    $filterByOperation = $services->where('operation', $operation);
                    $filterByOperation->when($filterByOperation->isNotEmpty(), static function ($filter) use ($waybills, $pointOfSale) {
                        $waybills->push(new Waybill(
                            $pointOfSale,
                            $filter->values(),
                            Carbon::now()
                        ));
                    });
                }
            }
        }

        return $waybills;
    }
}
