<?php

namespace Reports\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

interface ElasticSearchCriteria
{
    public function apply(ElasticQueryBuilder $elasticQueryBuilder): ElasticQueryBuilder;
}
