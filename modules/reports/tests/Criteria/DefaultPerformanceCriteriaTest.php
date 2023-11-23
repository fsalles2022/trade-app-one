<?php

namespace Reports\Tests\Criteria;

use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;
use Reports\Criteria\DefaultPerformanceCriteria;
use TradeAppOne\Tests\TestCase;

class DefaultPerformanceCriteriaTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_points_of_sale_per_cnpj_criteria()
    {
        $pointOfSaleFilter = new DefaultPerformanceCriteria([]);
        $className         = get_class($pointOfSaleFilter);
        $this->assertEquals(DefaultPerformanceCriteria::class, $className);
    }

    /** @test */
    public function should_apply_criteria_when_one_period_filter_exists()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = [
            'endDate' => '2018-09-11T22:36:18.394Z'
        ];

        $pointsOfSalePerCnpjCriteria = new DefaultPerformanceCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->once();

        $pointsOfSalePerCnpjCriteria->apply($criteriaMock);
    }

    /** @test */
    public function should_apply_criteria_when_one_filter_exists()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = ['pointsOfSale' => ['34234234']];

        $pointsOfSalePerCnpjCriteria = new DefaultPerformanceCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->once();

        $pointsOfSalePerCnpjCriteria->apply($criteriaMock);
    }

    /** @test */
    public function should_apply_criteria_twice_when_two_filters_exists()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = [
            'pointsOfSale' => ['34234234'],
            'startDate' => '2018-09-11T22:36:18.394Z',
            'endDate' => '2018-09-11T22:36:18.394Z'
        ];

        $pointsOfSalePerCnpjCriteria = new DefaultPerformanceCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->twice();

        $pointsOfSalePerCnpjCriteria->apply($criteriaMock);
    }

    /** @test */
    public function should_not_apply_criteria_when_constructor_is_null()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = null;

        $pointsOfSalePerCnpjCriteria = new DefaultPerformanceCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->never();

        $pointsOfSalePerCnpjCriteria->apply($criteriaMock);
    }
}
