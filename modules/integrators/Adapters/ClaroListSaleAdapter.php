<?php

declare(strict_types=1);

namespace Integrators\Adapters;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Models\Collections\Sale;

class ClaroListSaleAdapter
{
    public static function adapt(Collection $sales): Collection
    {
        $adaptedSales = [];

        foreach ($sales as $sale) {
            $adaptedSales[] = self::adaptSale($sale);
        }

        return collect($adaptedSales);
    }

    public static function adaptSale(Sale $sale): Collection
    {
        $saleCollection             = collect($sale->toArray());
        $saleCollection['services'] = self::adaptServices($sale->services);

        return $saleCollection;
    }

    /** @return array[] */
    public static function adaptServices(Collection $services): array
    {
        $adaptedServices = [];

        foreach ($services as $service) {
            $adaptedService['numeroContrato'] = (string) data_get($service, 'contractNumber');
            $adaptedService['codigoIbge']     = (string) data_get($service, 'ibgeCode');
            $adaptedService['dataInstalacao'] = (string) data_get($service, 'installationDate');

            $serviceCollection = collect($service->toArray())
                ->except([
                    'contractNumber',
                    'ibgeCode',
                    'installationDate'
                ]);

            $adaptedServices[] = $serviceCollection->merge($adaptedService)->toArray();
        }

        return $adaptedServices;
    }
}
