<?php

namespace TradeAppOne\Domain\Components\Elasticsearch;

use Elasticsearch\ClientBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class ElasticsearchConnection implements ElasticConnection
{
    private $client;
    private $params;

    const DEFAULT_TIME_BETWEEN_SCROLL_REQUESTS = "1m";

    public function __construct($index, $docType = null)
    {
        $this->client = ClientBuilder::create()->setHosts([config('elasticsearch.uri')])->build();

        if (is_null($index)) {
          //FIXME: Create Custom Exception
            throw new \InvalidArgumentException('Index need be not null.');
        }

        $params['index'] = $index;

        if (! is_null($docType)) {
            $params['type'] = $docType;
        }

        $this->params = $params;
    }

    public function execute(ElasticQueryBuilder $queryBuilder): array
    {
        $resolvedQueryBuilder = $queryBuilder->get();
        $mergedArray          = array_merge($this->params, $resolvedQueryBuilder->asArray());
        return $this->client->search($mergedArray);
    }

    public function executeUsingScroll(ElasticQueryBuilder $queryBuilder): array
    {
        $resolvedQueryBuilder = $queryBuilder->size(5000)->get();
        $mergedArray          = array_merge($this->params, $resolvedQueryBuilder->asArray());
        $scrolledArray        = array_merge(["scroll" => self::DEFAULT_TIME_BETWEEN_SCROLL_REQUESTS], $mergedArray);

        $response = $this->client->search($scrolledArray);

        $documents = [];

        while ($this->existsHits($response)) {
            $documents = array_merge($documents, $response['hits']['hits']);

            $scroll_id = data_get($response, '_scroll_id');

            $response = $this->client->scroll([
                    "scroll_id" => $scroll_id,
                    "scroll" => self::DEFAULT_TIME_BETWEEN_SCROLL_REQUESTS
                ]);
        }

        $response['hits']['hits'] = $documents;

        return $response;
    }

    /**
     * @param $response
     * @return bool
     */
    private function existsHits($response): bool
    {
        $hits = $response['hits']['hits'];
        return isset($hits) && count($hits) > 0;
    }
}
