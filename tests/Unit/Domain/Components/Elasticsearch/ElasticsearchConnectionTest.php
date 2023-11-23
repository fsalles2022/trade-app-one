<?php

namespace TradeAppOne\Tests\Unit\Domain\Components\Elasticsearch;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchConnection;
use TradeAppOne\Tests\TestCase;

class ElasticsearchConnectionTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_elasticsearch_connection()
    {
        $elasticsearchConnection = new ElasticsearchConnection('tao');
        $className               = get_class($elasticsearchConnection);
        $this->assertEquals(ElasticsearchConnection::class, $className);
    }

    /** @test */
    public function should_return_an_exception_when_invalid_constructed()
    {
        $index = null;
        $this->expectException(\InvalidArgumentException::class);
        $elasticsearchConnection = new ElasticsearchConnection($index);
    }
}
