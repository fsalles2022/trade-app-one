<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;
use TradeAppOne\Domain\Components\Helpers\MoneyHelper;

class FilterSalesByOperatorAdapter
{
    public static function adapt(string $operator, Collection $collection)
    {
        $operations = data_get($collection, 'aggregations.operation.buckets', []);
        return array_map(function ($operation) use ($operator) {
            return [
                'name' => trans("constants.operator.$operator." . data_get($operation, 'key')),
                'y' => data_get($operation, 'doc_count'),
                'sum_price' => MoneyHelper::formatMoney(data_get($operation, 'sum_price.value', 0))
            ];
        }, $operations);
    }
}
