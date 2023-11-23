<?php

namespace TradeAppOne\Domain\Components\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Responses\IndexResponse;

class ElasticSearch
{
    protected $client;
    protected $type;
    protected $index;
    protected $host;
    protected $port;

    public function __construct(ElasticSearchConfig $config)
    {
        $this->type  = $config->getType();
        $this->index = $config->getIndex();
        $this->host  = $config->getHost();
        $this->port  = $config->getPort();

        $this->client = $this->build();
    }

    public function index($body = [], ?string $id = null): IndexResponse
    {
        $response = $this->client->index([
            'id'    => $id,
            'type'  => $this->type,
            'index' => $this->index,
            'body'  => $body
        ]);

        return new IndexResponse($response);
    }

    public function search(array $body = []): array
    {
        return $this->client->search([
            'type'  => $this->type,
            'index' => $this->type,
            'body'  => $body
        ]);
    }

    public function ping(): bool
    {
        return $this->client->ping();
    }

    public function getUri(): string
    {
        return implode(':', [$this->host, $this->port]);
    }

    public function build(): Client
    {
        return ClientBuilder::create()
            ->setHosts([$this->getUri()])
            ->build();
    }
}
