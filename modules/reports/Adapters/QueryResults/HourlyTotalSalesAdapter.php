<?php

namespace Reports\Adapters\QueryResults;

use Illuminate\Support\Collection;

class HourlyTotalSalesAdapter
{
    public static function adapt(array $queryResult): Collection
    {
        return collect($queryResult['aggregations']['sales_over_day']['buckets'])->map(function ($pointOfSale) {
            $operators   = collect(data_get($pointOfSale, 'operator.buckets', []))->map(function ($operator) {
                return [
                    'key'    => $operator['key'],
                    'TOTAL'  => $operator['doc_count'],
                    'AMOUNT' => data_get($operator, 'sum_price.value', 0)
                ];
            });
            $total       = $operators->sum('TOTAL');
            $totalAmount = $operators->sum('AMOUNT');
            return [
                'key'       => $pointOfSale['key_as_string'],
                'TOTAL'     => $total,
                'AMOUNT'    => $totalAmount,
                'operators' => $operators
            ];
        });
    }
}
