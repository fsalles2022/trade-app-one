<?php

namespace Reports\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class StatusCriteria implements ElasticSearchCriteria
{
    private $statusList;

    const KEY = 'service_status';

    public function __construct(array $statusList)
    {
        $this->statusList = $statusList;
    }

    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder
    {
        $elasticQueryBuilder->whereIn(self::KEY, $this->statusList);
        return $elasticQueryBuilder;
    }
}
