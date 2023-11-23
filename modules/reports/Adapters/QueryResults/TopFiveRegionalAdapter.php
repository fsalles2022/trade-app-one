<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;
use Reports\Enum\OperationsGroupColors;
use TradeAppOne\Domain\Enumerators\GroupOfOperations;

class TopFiveRegionalAdapter
{
    public static function adapt(Collection $collection)
    {
        $listOfHierarchies = data_get($collection, 'aggregations.hierarchies.buckets');
        $hierarchies       = [];
        $data              = [
            GroupOfOperations::PRE_PAGO => [
                'color' => OperationsGroupColors::PRE_PAGO,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::PRE_PAGO),
                'data' => []
            ],
            GroupOfOperations::POS_PAGO => [
                'color' => OperationsGroupColors::POS_PAGO,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::POS_PAGO),
                'data' => []
            ],
            GroupOfOperations::CONTROLE => [
                'color' => OperationsGroupColors::CONTROLE,
                'name' => trans('constants.group_of_operations.' . GroupOfOperations::CONTROLE),
                'data' => []
            ]
        ];

        foreach ($listOfHierarchies as $hierarchy) {
            array_push($hierarchies, $hierarchy['key']);
            $prePlanCount      = data_get($hierarchy, GroupOfOperations::PRE_PAGO . '.doc_count', 0);
            $posPlanCount      = data_get($hierarchy, GroupOfOperations::POS_PAGO . '.doc_count', 0);
            $controlePlanCount = data_get($hierarchy, GroupOfOperations::CONTROLE . '.doc_count', 0);

            array_push($data[GroupOfOperations::PRE_PAGO]['data'], $prePlanCount);
            array_push($data[GroupOfOperations::POS_PAGO]['data'], $posPlanCount);
            array_push($data[GroupOfOperations::CONTROLE]['data'], $controlePlanCount);
        }

        return [
            'hierarchies' => $hierarchies,
            'data' => array_values($data),
        ];
    }
}
