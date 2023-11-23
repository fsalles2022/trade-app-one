<?php

namespace Reports\Tests\Criteria;

use Reports\Criteria\DefaultCriteria;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticQueryBuilder;
use TradeAppOne\Tests\TestCase;

class DefaultCriteriaTest extends TestCase
{
    /** @test */
    public function should_return_an_instance_of_points_of_sale_per_cnpj_criteria()
    {
        $pointOfSaleFilter = new DefaultCriteria([]);
        $className         = get_class($pointOfSaleFilter);
        $this->assertEquals(DefaultCriteria::class, $className);
    }

    /** @test */
    public function should_apply_criteria_when_one_filter_exists()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = ['pointsOfSale' => ['34234234']];

        $pointsOfSalePerCnpjCriteria = new DefaultCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->once();

        $pointsOfSalePerCnpjCriteria->apply($criteriaMock);
    }

    /** @test */
    public function should_not_apply_criteria_when_constructor_is_null()
    {
        $criteriaMock = \Mockery::spy(ElasticQueryBuilder::class);
        $filters      = null;

        $pointsOfSalePerCnpjCriteria = new DefaultCriteria($filters);

        $criteriaMock->shouldReceive('applyCriteria')->never();

        $pointsOfSalePerCnpjCriteria->apply($criteriaMock);
    }
}
