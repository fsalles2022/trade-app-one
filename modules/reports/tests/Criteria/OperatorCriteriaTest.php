<?php


namespace Reports\Tests\Criteria;

use Reports\Criteria\OperatorCriteria;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\TestCase;

class OperatorCriteriaTest extends TestCase
{
    /** @test */
    public function should_apply_criteria_filter_operator()
    {
        $elasticQuery  = new ElasticsearchQueryBuilder(resolve(ElasticConnection::class));
        $operators     = [Operations::CLARO];
        $queryExpectet = 'service_operator:(CLARO)';

        $operatorCriteria    = new OperatorCriteria($operators);
        $queryStringReceived = $elasticQuery->applyCriteria($operatorCriteria)->toStringQuery();

        $this->assertEquals($queryExpectet, $queryStringReceived);
    }
}
