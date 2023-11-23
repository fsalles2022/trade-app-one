<?php

namespace Reports\Adapters;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Fluent;

class SaleReportAdapter
{
    public static function adapt(Collection $elasticResponse): array
    {
        return array_pluck(data_get($elasticResponse, 'hits.hits'), '_source');
    }

    public static function paginate(Collection $elasticResponse, int $currentPage, int $perPage): LengthAwarePaginator
    {
        $total = data_get($elasticResponse, 'hits.total');
        $adapt = self::adapt($elasticResponse);

        return new LengthAwarePaginator(
            $adapt,
            $total,
            $perPage,
            $currentPage,
            ['path' => Request::url()]
        );
    }
}
