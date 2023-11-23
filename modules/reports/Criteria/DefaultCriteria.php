<?php

namespace Reports\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class DefaultCriteria implements ElasticSearchCriteria
{
    private $networks;
    private $status;
    private $pointsOfSale;

    public function __construct(?array $filters)
    {
        $this->networks = data_get($filters, 'networks');

        $this->status = data_get($filters, 'saleStatus');

        $this->pointsOfSale = data_get($filters, 'pointsOfSale');
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        if ($this->networks && ! $this->pointsOfSale) {
            $networksCriteria = new NetworksPerSlugCriteria($this->networks);
            $elasticQueryBuilder->applyCriteria($networksCriteria);
        }

        if ($this->status) {
            $statusCriteria = new StatusCriteria($this->status);
            $elasticQueryBuilder->applyCriteria($statusCriteria);
        }

        if ($this->pointsOfSale) {
            $pointOfSaleCriteria = new PointsOfSalePerCnpjCriteria($this->pointsOfSale);
            $elasticQueryBuilder->applyCriteria($pointOfSaleCriteria);
        }

        return $elasticQueryBuilder;
    }
}
