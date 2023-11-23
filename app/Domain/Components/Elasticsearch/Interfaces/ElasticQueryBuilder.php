<?php

namespace TradeAppOne\Domain\Components\Elasticsearch\Interfaces;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use Reports\Criteria\ElasticSearchCriteria;

interface ElasticQueryBuilder
{
    public function queryString(string $query): ElasticQueryBuilder;
    public function size(int $size): ElasticQueryBuilder;
    public function from(int $from): ElasticQueryBuilder;
    public function sort(string $field, string $order = 'desc'): ElasticQueryBuilder;
    public function aggregations(ElasticsearchAggregationStructure $aggs): ElasticQueryBuilder;
    public function whereIn(string $key, $values, string $glue = ' '): ElasticQueryBuilder;
    public function where(string $key, string $value): ElasticQueryBuilder;
    public function get(): ElasticQueryBuilder;
    public function applyCriteria(ElasticSearchCriteria $elasticSearchFilter): ElasticQueryBuilder;
    public function toStringQuery(): string;
    public function asJson(): string;
    public function asArray(): array;
}
