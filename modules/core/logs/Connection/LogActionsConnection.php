<?php

namespace Core\Logs\Connection;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticSearch;
use TradeAppOne\Domain\Components\Elasticsearch\Responses\IndexResponse;

class LogActionsConnection
{
    protected $elasticSearch;

    public function __construct(ElasticSearch $elasticSearch)
    {
        $this->elasticSearch = $elasticSearch;
    }

    public function save($record, $id = null): IndexResponse
    {
        return $this->elasticSearch->index($record, $id);
    }
}
