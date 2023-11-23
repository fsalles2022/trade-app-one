<?php

namespace Reports\Tests\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use Reports\Criteria\PointsOfSalePerCnpjCriteria;
use TradeAppOne\Tests\TestCase;

class PointsOfSalePerCnpjCriteriaTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_points_of_sale_per_cnpj_criteria()
    {
        $pointOfSaleFilter = new PointsOfSalePerCnpjCriteria([]);
        $className         = get_class($pointOfSaleFilter);
        $this->assertEquals(PointsOfSalePerCnpjCriteria::class, $className);
    }

    /** @test */
    public function should_apply_criteria_create_correct_query()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $cnpjs               = [ '012312312313', '12312312312', '131231231231' ];
        $queryStringExpected = "pointofsale_cnpj:(012312312313 12312312312 131231231231)";

        $pointsOfSalePerCnpjCriteria = new PointsOfSalePerCnpjCriteria($cnpjs);
        $queryStringReceived         = $elasticQueryBuilder->applyCriteria($pointsOfSalePerCnpjCriteria)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }
}
