<?php

namespace Reports\Tests\Helpers;

use Mockery;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;

trait ElasticSearchHelper
{
    public function mockElasticSearchConnection($elasticFixture)
    {
        $this->app->bind(ElasticConnection::class, function () use ($elasticFixture) {
            $elasticQueryBuilder = Mockery::mock(ElasticConnection::class)->makePartial();
            $elasticQueryBuilder->shouldReceive('execute')->andReturn($elasticFixture);
            $elasticQueryBuilder->shouldReceive('executeUsingScroll')->andReturn($elasticFixture);
            $elasticQueryBuilder->shouldReceive('executeWithoutContext')->andReturn($elasticFixture);

            return $elasticQueryBuilder;
        });
    }
}
