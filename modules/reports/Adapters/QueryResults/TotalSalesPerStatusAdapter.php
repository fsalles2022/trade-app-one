<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;
use Reports\Enum\ColorsOfTheSaleStatus;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;

class TotalSalesPerStatusAdapter
{
    public static function adapt(Collection $collection)
    {
        $listOfStatus = data_get($collection, 'aggregations.status_count.buckets', []);
        return array_map(function ($status) {
            return [
                'name' => trans('constants.sale.status.' . data_get($status, 'key')),
                'color' => ConstantHelper::getValue(ColorsOfTheSaleStatus::class, data_get($status, 'key')),
                'y' => data_get($status, 'doc_count')
            ];
        }, $listOfStatus);
    }
}
