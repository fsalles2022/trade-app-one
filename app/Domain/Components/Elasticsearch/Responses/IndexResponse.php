<?php

namespace TradeAppOne\Domain\Components\Elasticsearch\Responses;

class IndexResponse
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function isSuccess(): bool
    {
        $shards = $this->get('_shards');
        return data_get($shards, 'successful') === 1;
    }

    public function isFailed(): bool
    {
        $shards = $this->get('_shards');
        return data_get($shards, 'failed') === 0;
    }

    public function get(string $key = null)
    {
        return $key
            ? data_get($this->response, $key)
            : $this->response;
    }
}
