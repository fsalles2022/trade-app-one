<?php

namespace TradeAppOne\Domain\Components\Elasticsearch;

class ElasticsearchAggregationStructure extends ElasticsearchStructureAbstract
{
    const FIELD = 'field';
    const TERMS = 'terms';
    const SUM   = 'sum';
    const AVG   = 'avg';
    private $key;

    public function __construct($key)
    {
        parent::__construct();
        $this->key = $key;
    }

    /*
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-terms-aggregation.html
     */

    public function terms(
        string $field,
        array $options = ['size' => 10, 'order' => ['_count' => 'desc']]
    ): ElasticsearchAggregationStructure {
        $this->structure[$this->key][self::TERMS][self::FIELD] = $field;

        foreach ($options as $parameter => $value) {
            $this->structure[$this->key][self::TERMS][$parameter] = $value;
        }

        return $this;
    }

    /*
    * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-sum-aggregation.html
    */
    public function sum(string $field): ElasticsearchAggregationStructure
    {
        $this->structure[$this->key][self::SUM][self::FIELD] = $field;

        return $this;
    }

    public function sumToSameParentLevel(string $field, string $key = 'sum_price'): ElasticsearchAggregationStructure
    {
        $this->structure[$key][self::SUM][self::FIELD] = $field;

        return $this;
    }

    public function averageToSameParentLevel(
        string $field,
        string $key = 'average_balance'
    ): ElasticsearchAggregationStructure {

        $this->structure[$key][self::AVG][self::FIELD] = $field;

        return $this;
    }

    /*
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-metrics-valuecount-aggregation.html
     */
    public function valueCount(string $field, array $options = ['size' => 10]): ElasticsearchAggregationStructure
    {
        $this->structure[$this->key]['value_count'][self::FIELD] = $field;

        foreach ($options as $parameter => $value) {
            $this->structure[$this->key]['value_count'][$parameter] = $value;
        }

        return $this;
    }

    /*
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-range-aggregation.html
     */
    public function range(
        string $field,
        array $options = ['keyed' => true, 'ranges' => []]
    ): ElasticsearchAggregationStructure {
        $this->structure[$this->key]['range'][self::FIELD] = $field;

        foreach ($options as $parameter => $value) {
            $this->structure[$this->key]['range'][$parameter] = $value;
        }

        return $this;
    }

    /*
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-daterange-aggregation.html
     */
    public function dateRange(
        string $field,
        array $options = ['size' => 10, 'format' => 'yyyy-MM-dd', 'keyed' => true, 'ranges' => []]
    ): ElasticsearchAggregationStructure {
        $this->structure[$this->key]['date_range'][self::FIELD] = $field;

        foreach ($options as $parameter => $value) {
            $this->structure[$this->key]['date_range'][$parameter] = $value;
        }

        return $this;
    }

    /*
    https://www.elastic.co/guide/en/elasticsearch/reference/6.2/search-aggregations-metrics-cardinality-aggregation.html
    */
    public function cardinality(
        string $field,
        array $options = ['size' => 10, 'precision_threshold' => 3000]
    ): ElasticsearchAggregationStructure {
        $this->structure[$this->key]['cardinality'][self::FIELD] = $field;

        foreach ($options as $parameter => $value) {
            $this->structure[$this->key]['cardinality'][$parameter] = $value;
        }

        return $this;
    }

    /*
    * https://www.elastic.co/guide/en/elasticsearch/reference/6.2/search-aggregations-bucket-datehistogram-aggregation.html
    */
    public function dateHistogram(
        string $field,
        array $options = ['interval' => 'day', 'format' => 'yyyy-MM-dd'],
        string $timezone = ''
    ): ElasticsearchAggregationStructure {
        if (empty($timezone)) {
            $options['time_zone'] = config('elasticsearch.timezone');
        } else {
            $options['time_zone'] = $timezone;
        }

        $this->structure[$this->key]['date_histogram'][self::FIELD] = $field;

        foreach ($options as $parameter => $value) {
            $this->structure[$this->key]['date_histogram'][$parameter] = $value;
        }

        return $this;
    }

    /*
     *https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html
     */
    public function filterExists(string $field): ElasticsearchAggregationStructure
    {
        $this->structure[$this->key]['filter']['exists'][self::FIELD] = $field;

        return $this;
    }

    /*
     *https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-filter-aggregation.html
     */
    public function filterTermByFieldValue(string $field, $value): ElasticsearchAggregationStructure
    {
        $this->structure[$this->key]['filter']['terms'][$field] = $value;

        return $this;
    }

    public function nest(ElasticsearchAggregationStructure $nested): ElasticsearchAggregationStructure
    {
        $this->structure[$this->key]['aggs'] = $nested->asArray();

        return $this;
    }

    public function brother(ElasticsearchAggregationStructure $brother): ElasticsearchAggregationStructure
    {
        $arraysToAppend = $brother->asArray();
        $brotherKeys    = array_keys($arraysToAppend);
        foreach ($brotherKeys as $brotherKey) {
            $this->structure[$brotherKey] = $arraysToAppend[$brotherKey];
        }
        return $this;
    }

    public function brothers(array $brothers): ElasticsearchAggregationStructure
    {
        foreach ($brothers as $brother) {
            if ($brother instanceof ElasticsearchAggregationStructure) {
                $arrayToAppend = $brother->asArray();
                $brotherKey    = key($arrayToAppend);

                $this->structure[$brotherKey] = $arrayToAppend[$brotherKey];
            }
        }

        return $this;
    }

    public function raw(array $array)
    {
        $this->structure = $array;
        return $this;
    }
}
