<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;
use Reports\Enum\ColorsOperators;
use Reports\Helpers\ColorHelper;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;

class MonthSalesPerStateAdapter
{
    public static function adapt(Collection $collection)
    {
        $operatorsList = [];
        $listOfStates  = data_get($collection, 'aggregations.state_count.buckets', []);
        $data          = [];
        $states        = [];
        foreach ($listOfStates as $index => $state) {
            array_push($states, $state['key']);
            $operators = data_get($state, 'operator_count.buckets');
            foreach ($operators as $operator) {
                $operatorLabel = $operator['key'];
                $operations    = data_get($operator, 'operation_count.buckets');
                data_set($operatorsList, $operatorLabel, 0.1, false);
                foreach ($operations as ['key' => $operation, 'doc_count' => $quantity]) {
                    if (array_key_exists($operation, $data)) {
                        $data[$operation]['data'][$index] = $quantity;
                    } else {
                        data_set($data, $operation, [
                            'name' => self::getName($operatorLabel, $operation),
                            'data' => array_fill(0, count($listOfStates), 0),
                            'stack' => $operatorLabel,
                            'color' => self::getColor($operatorLabel, $operatorsList[$operatorLabel])
                        ]);
                        data_set($data, "$operation.data.$index", $quantity);
                        $operatorsList[$operatorLabel] += 0.1;
                    }
                }
            }
        }
        return [
            'states' => $states,
            'data' => collect($data)->sortBy('stack')->values()->toArray()
        ];
    }

    private static function getName(string $operator, string $operation): string
    {
        return ucwords(strtolower($operator)) .' - ' . trans("constants.operator.$operator." . $operation);
    }

    private static function getColor(string $operator, $percentage)
    {
        return ColorHelper::getBrightenColor(ConstantHelper::getValue(ColorsOperators::class, $operator), $percentage);
    }
}
