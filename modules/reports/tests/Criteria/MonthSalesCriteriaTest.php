<?php

namespace Reports\Tests\Criteria;

use Reports\Criteria\MonthSalesCriteria;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;
use TradeAppOne\Tests\TestCase;

class MonthSalesCriteriaTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_points_of_sale_per_cnpj_criteria()
    {
        $pointOfSaleFilter = new MonthSalesCriteria([]);
        $className         = get_class($pointOfSaleFilter);
        $this->assertEquals(MonthSalesCriteria::class, $className);
    }

    /** @test */
    public function should_apply_criteria_when_initialDate_exists()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = [
            'startDate' => '2018-09-01T22:36:18.394Z'
        ];

        $monthSalesCriteria = new MonthSalesCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->once();

        $monthSalesCriteria->apply($criteriaMock);
    }

    /** @test */
    public function should_apply_criteria_when_endDate_exists()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = [
            'endDate' => '2018-09-30T22:36:18.394Z'
        ];

        $monthSalesCriteria = new MonthSalesCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->once();

        $monthSalesCriteria->apply($criteriaMock);
    }

    /** @test */
    public function should_apply_criteria_when_constructor_is_null()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = null;

        $pointsOfSalePerCnpjCriteria = new MonthSalesCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->once();

        $pointsOfSalePerCnpjCriteria->apply($criteriaMock);
    }
}
