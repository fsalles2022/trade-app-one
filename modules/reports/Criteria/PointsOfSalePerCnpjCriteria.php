<?php

namespace Reports\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class PointsOfSalePerCnpjCriteria implements ElasticSearchCriteria
{
    private $cnpjs;
    const KEY = 'pointofsale_cnpj';

    public function __construct(array $cnpjsFromPointsOfSale)
    {
        $this->cnpjs = $cnpjsFromPointsOfSale;
    }

    public function apply(ElasticQueryBuilder $elasticSearchQueryBuilder): ElasticQueryBuilder
    {
        $elasticSearchQueryBuilder->whereIn(self::KEY, $this->cnpjs);
        return $elasticSearchQueryBuilder;
    }
}
