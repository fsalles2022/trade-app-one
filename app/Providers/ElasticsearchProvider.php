<?php

namespace TradeAppOne\Providers;

use Illuminate\Support\Facades\Broadcast;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchConnection;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;

class ElasticsearchProvider extends ServiceProvider
{
    const ELASTIC_SEARCH_INDEX = 'tao';

    public function register()
    {
        app()->bind(ElasticConnection::class, function () {
            return new ElasticsearchConnection(self::ELASTIC_SEARCH_INDEX);
        });
        
        app()->bind(ElasticQueryBuilder::class, function () {
            return new ElasticsearchQueryBuilder();
        });

        app()->bind(ClientBuilder::class, function () {
            $host = config('heimdall.host') . ':' . config('heimdall.port');
            return ClientBuilder::create()->setHosts([$host])->build();
        });
    }
}
