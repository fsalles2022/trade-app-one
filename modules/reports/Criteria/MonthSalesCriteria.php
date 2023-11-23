<?php

namespace Reports\Criteria;

use Carbon\Carbon;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class MonthSalesCriteria implements ElasticSearchCriteria
{
    private $startDate;
    private $endDate;

    public function __construct(?array $filters)
    {
        $startDate       = data_get($filters, 'startDate');
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;

        $endDate       = data_get($filters, 'endDate');
        $this->endDate = $endDate ? Carbon::parse($endDate) : null;
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        if ($this->startDate || $this->endDate) {
            if (is_null($this->startDate)) {
                $this->startDate = $this->endDate->toDateString();
                $this->startDate = Carbon::parse($this->startDate)->startOfMonth();
            } elseif (is_null($this->endDate)) {
                $this->endDate = $this->startDate->toDateString();
                $this->endDate = Carbon::parse($this->endDate)->endOfMonth();
            }
        } else {
            $this->startDate = (Carbon::now())->startOfMonth();
            $this->endDate   = (Carbon::now());
        }

        $periodCriteria = new PeriodCriteria($this->startDate, $this->endDate);
        $elasticQueryBuilder->applyCriteria($periodCriteria);

        return $elasticQueryBuilder;
    }

    public function applyIfDatesNotExists(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        if (empty($this->startDate) and empty($this->endDate)) {
            return $this->apply($elasticQueryBuilder);
        }

        return $elasticQueryBuilder;
    }
}
