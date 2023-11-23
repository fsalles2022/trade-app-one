<?php

namespace Reports\Adapters\QueryResults;

use Reports\Services\TopFiveHierarchyService;

class TopFiveHierarchyAdapter
{
    public static function adapt($queryResult)
    {
        $hierarchies = collect(data_get(
            $queryResult,
            'aggregations.' . TopFiveHierarchyService::HIERARCHIES . '.buckets',
            []
        ));

        return $hierarchies->map(function ($hierarchy) {
            $pos      = collect(data_get($hierarchy, 'POS_PAGO.buckets', []));
            $pre      = collect(data_get($hierarchy, 'PRE_PAGO.buckets', []));
            $totalPos = $pos->sum('doc_count');
            $totalPre = $pre->sum('doc_count');
            return [
                'key'      => $hierarchy['key'],
                'PRE_PAGO' => [
                    'total'      => $totalPre,
                    'operations' => $pre
                ],
                'POS_PAGO' => [
                    'total'      => $totalPos,
                    'operations' => $pos
                ]
            ];
        });
    }
}
