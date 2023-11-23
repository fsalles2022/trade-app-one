<?php

namespace Reports\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class OperatorCriteria implements ElasticSearchCriteria
{
    private $operators;
    const OPERATOR = 'service_operator';

    public function __construct(array $operators)
    {
        $this->operators = $operators;
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        $elasticQueryBuilder->whereIn(self::OPERATOR, $this->operators);
        return $elasticQueryBuilder;
    }
}
