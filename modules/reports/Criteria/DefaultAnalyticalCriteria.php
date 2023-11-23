<?php

namespace Reports\Criteria;

use Carbon\Carbon;
use Reports\Enum\GroupOfStatus;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class DefaultAnalyticalCriteria implements ElasticSearchCriteria
{
    private $networks;
    private $pointsOfSale;
    private $hierarchies;
    private $startDate;
    private $endDate;
    private $status;
    private $operator;
    private $operation;

    public function __construct(?array $filters)
    {
        $this->networks = data_get($filters, 'networks');

        $this->pointsOfSale = data_get($filters, 'pointsOfSale');

        $startDate       = data_get($filters, 'startDate');
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;

        $endDate       = data_get($filters, 'endDate');
        $this->endDate = $endDate ? Carbon::parse($endDate) : null;

        $this->status = data_get($filters, 'status');

        $this->hierarchies = data_get($filters, 'hierarchies');

        $this->operation = data_get($filters, 'operations');

        $this->operator = data_get($filters, 'operators');
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        if ($this->networks && ! $this->pointsOfSale) {
            $networksCriteria = new NetworksPerSlugCriteria($this->networks);
            $elasticQueryBuilder->applyCriteria($networksCriteria);
        }

        if ($this->pointsOfSale) {
            $pointOfSaleFilter = new PointsOfSalePerCnpjCriteria($this->pointsOfSale);
            $elasticQueryBuilder->applyCriteria($pointOfSaleFilter);
        }

        if ($this->startDate || $this->endDate) {
            $periodCriteria = new PeriodCriteria($this->startDate, $this->endDate);
            $elasticQueryBuilder->applyCriteria($periodCriteria);
        }

        if ($this->status) {
            $statusFilter = new StatusCriteria($this->status);
            $elasticQueryBuilder->applyCriteria($statusFilter);
        }

        if ($this->hierarchies && ! $this->pointsOfSale) {
            $hierarchiesCriteria = new HierarchiesCriteria($this->hierarchies);
            $elasticQueryBuilder->applyCriteria($hierarchiesCriteria);
        }

        if ($this->operation) {
            $operationCriteria = new OperationCriteria($this->operation);
            $elasticQueryBuilder->applyCriteria($operationCriteria);
        }

        if ($this->operator) {
            $operatorCriteria = new OperatorCriteria($this->operator);
            $elasticQueryBuilder->applyCriteria($operatorCriteria);
        }

        $statusCriteria = new StatusCriteria(GroupOfStatus::PERFORMED_SALES);
        $elasticQueryBuilder->applyCriteria($statusCriteria);

        return $elasticQueryBuilder;
    }
}
