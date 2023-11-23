<?php

namespace Reports\Tests\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Enumerators\ServiceStatus;
use Reports\Criteria\StatusCriteria;
use TradeAppOne\Tests\TestCase;

class StatusCriteriaTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_status_criteria()
    {
        $pointOfSaleFilter = new StatusCriteria([]);
        $className         = get_class($pointOfSaleFilter);

        $this->assertEquals(StatusCriteria::class, $className);
    }

    /** @test */
    public function should_apply_criteria_status_query()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder();

        $queryStringExpected = 'service_status:(ACCEPTED APPROVED CANCELED)';
        $filteredStatus      = [ServiceStatus::ACCEPTED, ServiceStatus::APPROVED, ServiceStatus::CANCELED];

        $statusCriteria      = new StatusCriteria($filteredStatus);
        $queryStringReceived = $elasticQueryBuilder->applyCriteria($statusCriteria)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }
}
