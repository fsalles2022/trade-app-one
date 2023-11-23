<?php

namespace TradeAppOne\Tests\Unit\Domain\Components\Elasticsearch;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Repositories\QueryObject\FilteredQueryObject;
use TradeAppOne\Tests\TestCase;

class FilteredQueryObjectTest extends TestCase
{

    /** @test */
    public function should_be_instance_of_filtered_query_object()
    {
        $filteredQueryObject = new FilteredQueryObject(new ElasticsearchQueryBuilder(), []);
        $className           = get_class($filteredQueryObject);

        $this->assertEquals(FilteredQueryObject::class, $className);
    }

    /** @test */
    public function should_get_query_return_an_instance_of_elastic_search_query_builder()
    {
        $filteredQueryObject = new FilteredQueryObject(new ElasticsearchQueryBuilder(), []);
        $elasticQueryBuilder = $filteredQueryObject->getQuery();
        $className           = get_class($elasticQueryBuilder);

        $this->assertEquals(ElasticsearchQueryBuilder::class, $className);
    }

    /** @test */
    public function should_return_query_builder_with_filter_of_points_of_sale_only()
    {
        $filters = ['pointsOfSale' => ['MRB', 'SCN', '16', '861']];

        $filteredQueryObject = new FilteredQueryObject(new ElasticsearchQueryBuilder(), $filters);
        $expectedStringQuery = 'pointofsale_cnpj:(MRB SCN 16 861)';

        $receivedStringQuery = $filteredQueryObject->getQuery()->toStringQuery();

        $this->assertEquals($expectedStringQuery, $receivedStringQuery);
    }

    /** @test */
    public function should_return_plain_query_builder_when_filters_not_exists()
    {
        $filters             = ['greatFilter' => ['MRB', 'SCN', '16', '861']];
        $filteredQueryObject = new FilteredQueryObject(new ElasticsearchQueryBuilder(), $filters);

        $receivedStringQuery = $filteredQueryObject->getQuery()->toStringQuery();

        $this->assertEquals('', $receivedStringQuery);
    }

    /** @test */
    public function should_return_only_valid_filters_when_exists_values_as_array()
    {
        $filters             = ['pointsOfSale' => ['MRB', 'SCN', '16', ['cnpj' => '861']]];
        $filteredQueryObject = new FilteredQueryObject(new ElasticsearchQueryBuilder(), $filters);

        $receivedStringQuery = $filteredQueryObject->getQuery()->toStringQuery();

        $this->assertEquals('', $receivedStringQuery);
    }

    /** @test */
    public function should_return_only_valid_filters_when_exists_values_as_object()
    {
        $object       = new \StdClass();
        $object->cnpj = '2312312312414';

        $filters             = ['pointsOfSale' => ['MRB', 'SCN', '16', $object]];
        $filteredQueryObject = new FilteredQueryObject(new ElasticsearchQueryBuilder(), $filters);

        $receivedStringQuery = $filteredQueryObject->getQuery()->toStringQuery();

        $this->assertEquals('', $receivedStringQuery);
    }

    /** @test */
    public function should_return_query_builder_with_filter_of_points_of_sale_and_operations()
    {
        $filters             = [
            'pointsOfSale' => [ 'MRB', 'SCN', '16', '861'],
            'operators' => ['CLARO', 'VIVO']
        ];
        $filteredQueryObject = new FilteredQueryObject(new ElasticsearchQueryBuilder(), $filters);
        $expectedStringQuery = 'pointofsale_cnpj:(MRB SCN 16 861) AND service_operators:(CLARO VIVO)';

        $receivedStringQuery = $filteredQueryObject->getQuery()->toStringQuery();

        $this->assertEquals($expectedStringQuery, $receivedStringQuery);
    }
}
