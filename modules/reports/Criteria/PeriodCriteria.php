<?php

namespace Reports\Criteria;

use Carbon\Carbon;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class PeriodCriteria implements ElasticSearchCriteria
{
    private $startDate;
    private $endDate;

    public function __construct(?Carbon $startDate, ?Carbon $endDate)
    {
        if ($startDate) {
            $startDate->tz   = config('app.timezone');
            $startDateString = $startDate ? $startDate->toIso8601String() : '*';
        } else {
            $startDateString = '*';
        }
        if ($endDate) {
            $endDate->tz   = config('app.timezone');
            $endDateString = $endDate->toIso8601String();
        } else {
            $endDateString = '*';
        }

        $this->startDate = $startDateString;
        $this->endDate   = $endDateString;
    }

    public function apply(ElasticQueryBuilder $elasticSearchQueryBuilder): ElasticQueryBuilder
    {
        return $elasticSearchQueryBuilder->where('created_at', "[{$this->startDate} TO {$this->endDate}]");
    }
}
