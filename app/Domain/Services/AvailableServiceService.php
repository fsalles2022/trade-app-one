<?php

namespace TradeAppOne\Domain\Services;

use Illuminate\Database\Eloquent\Builder;
use TradeAppOne\Domain\Models\Tables\AvailableService;
use TradeAppOne\Domain\Models\Tables\PointOfSale;

class AvailableServiceService extends BaseService
{

    public static function getAvailableService(PointOfSale $pointOfSale, array $operationInProgress): AvailableService
    {
        if ($pointOfSale->availableServicesRelation()->count()) {
            $availableService = $pointOfSale->availableServicesRelation()->whereHas('service', static function (Builder $query) use ($operationInProgress) {
                $query->where($operationInProgress);
            })->get()->first();
        } else {
            $availableService = $pointOfSale->network->availableServicesRelation()->whereHas('service', static function (Builder $query) use ($operationInProgress) {
                $query->where($operationInProgress);
            })->get()->first();
        }

        return $availableService;
    }

    public static function getOldFormatAvailableServices(array $availableServices):array
    {
        $availableServicesReturn = [];
        foreach ($availableServices as $service) {
            $availableServicesReturn[$service['sector']][$service['operator']][] = $service['operation'];
        }
        return $availableServicesReturn;
    }
}
