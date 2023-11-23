<?php

namespace Reports\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class OperationCriteria implements ElasticSearchCriteria
{
    private $operations;
    const OPERATION = 'service_operation';

    public function __construct(array $operations)
    {
        $this->operations = $operations;
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        $elasticQueryBuilder->whereIn(self::OPERATION, $this->operations);
        return $elasticQueryBuilder;
    }
}
