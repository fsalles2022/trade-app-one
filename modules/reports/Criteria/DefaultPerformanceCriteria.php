<?php

namespace Reports\Criteria;

use Carbon\Carbon;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class DefaultPerformanceCriteria implements ElasticSearchCriteria
{
    private $networks;
    private $hierarchies;
    private $pointsOfSale;
    private $startDate;
    private $status;
    private $endDate;

    public function __construct(?array $filters)
    {
        $this->networks = data_get($filters, 'networks');

        $this->hierarchies = data_get($filters, 'hierarchies');

        $this->pointsOfSale = data_get($filters, 'pointsOfSale');

        $this->status = data_get($filters, 'saleStatus');

        $startDate       = data_get($filters, 'startDate');
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;

        $endDate       = data_get($filters, 'endDate');
        $this->endDate = $endDate ? Carbon::parse($endDate) : null;
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        if ($this->networks && ! $this->hierarchies && ! $this->pointsOfSale) {
            $networksCriteria = new NetworksPerSlugCriteria($this->networks);
            $elasticQueryBuilder->applyCriteria($networksCriteria);
        }

        if ($this->hierarchies && ! $this->pointsOfSale) {
            $hierarchiesCriteria = new HierarchiesCriteria($this->hierarchies);
            $elasticQueryBuilder->applyCriteria($hierarchiesCriteria);
        }

        if ($this->pointsOfSale) {
            $pointOfSaleCriteria = new PointsOfSalePerCnpjCriteria($this->pointsOfSale);
            $elasticQueryBuilder->applyCriteria($pointOfSaleCriteria);
        }

        if ($this->status) {
            $statusCriteria = new StatusCriteria($this->status);
            $elasticQueryBuilder->applyCriteria($statusCriteria);
        }

        if ($this->startDate || $this->endDate) {
            $periodCriteria = new PeriodCriteria($this->startDate, $this->endDate);
            $elasticQueryBuilder->applyCriteria($periodCriteria);
        }

        return $elasticQueryBuilder;
    }
}
