<?php

namespace TradeAppOne\Domain\Components\Elasticsearch\Interfaces;

interface ElasticConnection
{
    public function execute(ElasticQueryBuilder $elasticConnection): array;
    public function executeUsingScroll(ElasticQueryBuilder $elasticConnection): array;
}
