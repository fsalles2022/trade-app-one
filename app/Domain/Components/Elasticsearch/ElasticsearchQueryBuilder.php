<?php

namespace TradeAppOne\Domain\Components\Elasticsearch;

use Reports\Criteria\ElasticSearchCriteria;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class ElasticsearchQueryBuilder extends ElasticsearchStructureAbstract implements ElasticQueryBuilder
{
    private $query = '';

    const TYPE_AND       = 'AND';
    const TYPE_OR        = 'OR';
    const DEFAULT_SEARCH = '*';
    const DEFAULT_SIZE   = 10000000;
    const EXISTS         = '_exists_';

    public function __construct()
    {
        parent::__construct();

        $this->structure = [
            "size" => self::DEFAULT_SIZE,
            'body' => [
                'query' => [
                    'query_string' => [
                        'default_field' => '_all',
                        'query' => '*'
                    ]
                ]
            ]
        ];
    }

    public function queryString(string $query): ElasticQueryBuilder
    {
        $this->structure['body']['query']['query_string']['query'] = $query;

        return $this;
    }

    public function size(int $size): ElasticQueryBuilder
    {
        $this->structure['size'] = $size;

        return $this;
    }

    public function from(int $from): ElasticQueryBuilder
    {
        $this->structure['from'] = $from;

        return $this;
    }

    public function sort(string $field, string $order = 'desc'): ElasticQueryBuilder
    {
        $this->structure['body']['sort'] = [[$field => ['order' => $order]]];

        return $this;
    }

    public function aggregations(ElasticsearchAggregationStructure $aggs): ElasticQueryBuilder
    {
        $this->structure['body']['aggs'] = $aggs->asArray();

        return $this;
    }

    public function whereIn(string $key, $values, $glue = ' ') :ElasticQueryBuilder
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key can not be empty.');
        }

        if (! empty(trim($glue))) {
            $glue = " ${glue} ";
        }

        if (is_array($values) && ! empty($values)) {
            $valuesImploded = implode($glue, $values);
        } else {
            $valuesImploded = '0';
        }

        $queryString = "${key}:(${valuesImploded})";

        $this->mergeCondition($queryString, self::TYPE_AND);
        return $this;
    }

    public function exists(string $key) :ElasticQueryBuilder
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key/Value can not be empty.');
        }

        $queryString = self::EXISTS . ":{$key}";
        $this->mergeCondition($queryString, self::TYPE_AND);
        return $this;
    }

    public function where(string $key, string $value) :ElasticQueryBuilder
    {
        if (empty($key) || empty($value)) {
            throw new \InvalidArgumentException('Key/Value can not be empty.');
        }

        $queryString = "${key}:${value}";

        $this->mergeCondition($queryString, self::TYPE_AND);
        return $this;
    }

    private function mergeCondition(string $condition, string $operator) :void
    {
        if (empty($this->query)) {
            $this->query = $condition;
        } else {
            $actualQuery = $this->query;
            $this->query = $actualQuery . " ${operator} ${condition}";
        }
    }

    public function toStringQuery(): string
    {
        return $this->query;
    }

    public function applyCriteria(ElasticSearchCriteria $elasticSearchFilter) :ElasticQueryBuilder
    {
        return $elasticSearchFilter->apply($this);
    }

    public function get(): ElasticQueryBuilder
    {
        $this->structure['body']['query']['query_string']['query'] = empty($this->query) ? self::DEFAULT_SEARCH : $this->query;
        return $this;
    }
}
