<?php

namespace Reports\Tests\Criteria;

use Carbon\Carbon;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use Reports\Criteria\PeriodCriteria;
use TradeAppOne\Tests\TestCase;

class PeriodCriteriaTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_period_criteria()
    {
        $pointOfSaleFilter = new PeriodCriteria(null, null);
        $className         = get_class($pointOfSaleFilter);

        $this->assertEquals(PeriodCriteria::class, $className);
    }

    /** @test */
    public function should_apply_criteria_create_since_period()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $queryStringExpected = 'created_at:[2008-01-01T00:00:00-02:00 TO *]';
        $carbon              = new Carbon('first day of January 2008', 'America/Sao_paulo');

        $pointsOfSalePerCnpjCriteria = new PeriodCriteria($carbon, null);
        $queryStringReceived         = $elasticQueryBuilder->applyCriteria($pointsOfSalePerCnpjCriteria)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_apply_criteria_create_until_period()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $carbon              = new Carbon('first day of January 2008', 'America/Vancouver');
        $queryStringExpected = 'created_at:[* TO 2008-01-01T06:00:00-02:00]';

        $pointsOfSalePerCnpjCriteria = new PeriodCriteria(null, $carbon);
        $queryStringReceived         = $elasticQueryBuilder->applyCriteria($pointsOfSalePerCnpjCriteria)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_apply_criteria_create_between_period()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $carbon              = new Carbon('first day of January 2008', 'America/Vancouver');
        $queryStringExpected = 'created_at:[2008-01-01T06:00:00-02:00 TO 2008-01-01T06:00:00-02:00]';

        $pointsOfSalePerCnpjCriteria = new PeriodCriteria($carbon, $carbon);
        $queryStringReceived         = $elasticQueryBuilder->applyCriteria($pointsOfSalePerCnpjCriteria)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_apply_criteria_create_without_period()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $queryStringExpected = 'created_at:[* TO *]';

        $pointsOfSalePerCnpjCriteria = new PeriodCriteria(null, null);
        $queryStringReceived         = $elasticQueryBuilder->applyCriteria($pointsOfSalePerCnpjCriteria)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }
}
