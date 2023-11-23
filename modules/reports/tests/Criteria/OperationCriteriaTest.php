<?php


namespace Reports\Tests\Criteria;

use Reports\Criteria\OperationCriteria;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Tests\TestCase;

class OperationCriteriaTest extends TestCase
{
    /** @test */
    public function should_apply_criteria_filter_operation()
    {
        $elasticQuery  = new ElasticsearchQueryBuilder(resolve(ElasticConnection::class));
        $operations    = [Operations::CLARO_CONTROLE_BOLETO];
        $queryExpectet = 'service_operation:(CONTROLE_BOLETO)';

        $operatorCriteria    = new OperationCriteria($operations);
        $queryStringReceived = $elasticQuery->applyCriteria($operatorCriteria)->toStringQuery();

        $this->assertEquals($queryExpectet, $queryStringReceived);
    }
}
