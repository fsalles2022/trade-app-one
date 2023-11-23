<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;
use Reports\Enum\OperationsGroupColors;
use TradeAppOne\Domain\Enumerators\GroupOfOperations;

class SalesByNetworkPlansTelecommunicationAdapter
{
    public static function adapt(Collection $collection)
    {
        $listOfNetworks = data_get($collection, 'aggregations.networks.buckets');
        $networks       = [];
        $data           = [
            GroupOfOperations::PRE_PAGO => [
                'color' => OperationsGroupColors::PRE_PAGO,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::PRE_PAGO),
                'data' => []
            ],
            GroupOfOperations::POS_PAGO => [
                'color' => OperationsGroupColors::POS_PAGO,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::POS_PAGO),
                'data' => []
            ]
        ];
        foreach ($listOfNetworks as $network) {
            array_push($networks, ucwords(strtolower($network['key'])));
            $prePlanCount = data_get($network, GroupOfOperations::PRE_PAGO . '.doc_count', 0);
            $posPlanCount = data_get($network, GroupOfOperations::POS_PAGO . '.doc_count', 0);

            array_push($data[GroupOfOperations::PRE_PAGO]['data'], $prePlanCount);
            array_push($data[GroupOfOperations::POS_PAGO]['data'], $posPlanCount);
        }
        return [
            'networks' => $networks,
            'data' => array_values($data)
        ];
    }
}
