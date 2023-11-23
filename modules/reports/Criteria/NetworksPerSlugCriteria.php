<?php

namespace Reports\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class NetworksPerSlugCriteria implements ElasticSearchCriteria
{
    private $slugs;
    const KEY = 'pointofsale_network_slug';

    public function __construct(array $slugsFromNetworks)
    {
        $this->slugs = $slugsFromNetworks;
    }

    public function apply(ElasticQueryBuilder $elasticSearchQueryBuilder): ElasticQueryBuilder
    {
        $elasticSearchQueryBuilder->whereIn(self::KEY, $this->slugs);
        return $elasticSearchQueryBuilder;
    }
}
