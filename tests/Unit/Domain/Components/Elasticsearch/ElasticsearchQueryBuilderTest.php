<?php

namespace TradeAppOne\Tests\Unit\Domain\Components\Elasticsearch;

use Mockery;
use Reports\Criteria\ElasticSearchCriteria;
use Reports\Criteria\PointsOfSalePerCnpjCriteria;
use Reports\Tests\Fixture\ElasticSearchFixture;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchQueryBuilder;
use TradeAppOne\Domain\Components\Elasticsearch\Interfaces\ElasticConnection;
use TradeAppOne\Tests\TestCase;

class ElasticsearchQueryBuilderTest extends TestCase
{
    /** @test */
    public function should_query_string_set_to_structure_correctly()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $queryString         = 'TESTE:1';
        $elasticQueryBuilder->queryString($queryString);
        $builderAsArray = $elasticQueryBuilder->asArray();

        $this->assertEquals($queryString, $builderAsArray['body']['query']['query_string']['query']);
    }

    /** @test */
    public function should_size_set_to_structure_correctly()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $size                = 50;
        $elasticQueryBuilder->size($size);
        $builderAsArray = $elasticQueryBuilder->asArray();

        $this->assertEquals($size, $builderAsArray['size']);
    }

    /** @test */
    public function should_aggregations_set_to_structure_correctly()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $aggs                = new ElasticsearchAggregationStructure('key');

        $elasticQueryBuilder->aggregations($aggs->terms('term'));
        $builderAsArray = $elasticQueryBuilder->asArray();

        $this->assertArrayHasKey('key', $builderAsArray['body']['aggs']);
    }

    /** @test */
    public function should_execute_return_array_from_client()
    {
        $elasticQueryBuilder = Mockery::mock(ElasticsearchQueryBuilder::class)->makePartial();
        $elasticFixture      = ElasticSearchFixture::getSaleArray();
        $elasticQueryBuilder->shouldReceive('execute')->once()->andReturn($elasticFixture);

        $response = $elasticQueryBuilder->execute();
        $this->assertArrayHasKey('hits', $response['hits']);
    }

    /** @test */
    public function should_apply_criteria_return_elastic_search_query_builder()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $pointOfSaleFilter   = new PointsOfSalePerCnpjCriteria([]);

        $result    = $elasticQueryBuilder->applyCriteria($pointOfSaleFilter);
        $className = get_class($result);

        $this->assertEquals(ElasticsearchQueryBuilder::class, $className);
    }

    /** @test */
    public function should_apply_criteria_return_query_with_cnpj()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $criteriaMock        = Mockery::spy(ElasticSearchCriteria::class);

        $criteriaMock->shouldReceive('apply')->with($elasticQueryBuilder)->once();

        $elasticQueryBuilder->applyCriteria($criteriaMock);
    }

    /** @test */
    public function should_where_in_return_elastic_search_query_builder()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $array               = ['1111111111', '2222222222'];
        $key                 = 'pointOfSale';

        $result    = $elasticQueryBuilder->whereIn($key, $array);
        $className = get_class($result);

        $this->assertEquals(ElasticsearchQueryBuilder::class, $className);
    }

    /** @test */
    public function should_where_in_return_exception_when_empty_key_and_values_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $array               = [];
        $key                 = '';

        $this->expectException(\InvalidArgumentException::class);
        $value = $elasticQueryBuilder->whereIn($key, $array);
    }

    /** @test */
    public function should_where_in_return_0_query_when_empty_array_values_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $array               = [];
        $key                 = 'pointOfSale';
        $queryStringExpected = "pointOfSale:(0)";

        $queryStringReceived = $elasticQueryBuilder->whereIn($key, $array)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_where_in_return_0_query_when_empty_string_values_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $array               = '';
        $key                 = 'pointOfSale';
        $queryStringExpected = "pointOfSale:(0)";

        $queryStringReceived = $elasticQueryBuilder->whereIn($key, $array)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_where_in_return_0_query_when_null_values_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $array               = null;
        $key                 = 'pointOfSale';
        $queryStringExpected = "pointOfSale:(0)";

        $queryStringReceived = $elasticQueryBuilder->whereIn($key, $array)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_where_in_return_exception_when_empty_key_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $array               = ['1111111111', '2222222222'];
        $key                 = '';

        $this->expectException(\InvalidArgumentException::class);
        $elasticQueryBuilder->whereIn($key, $array);
    }

    /** @test */
    public function should_where_in_return_valid_query()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $array               = ['1111111111', '2222222222'];
        $key                 = 'pointOfSale';
        $queryStringExpected = "pointOfSale:(1111111111 2222222222)";

        $queryStringReceived = $elasticQueryBuilder->whereIn($key, $array)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_where_return_exception_when_empty_key_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $key                 = '';
        $value               = '213231';
        $queryStringExpected = "pointOfSale:(1111111111 2222222222)";

        $this->expectException(\InvalidArgumentException::class);
        $elasticQueryBuilder->where($key, $value);
    }

    /** @test */
    public function should_where_return_exception_when_empty_value_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $key                 = 'key';
        $value               = '';
        $queryStringExpected = "pointOfSale:(1111111111 2222222222)";

        $this->expectException(\InvalidArgumentException::class);
        $elasticQueryBuilder->where($key, $value);
    }

    /** @test */
    public function should_where_return_exception_when_empty_key_and_value_send()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $key                 = '';
        $value               = '';
        $queryStringExpected = "pointOfSale:(1111111111 2222222222)";

        $this->expectException(\InvalidArgumentException::class);
        $elasticQueryBuilder->where($key, $value);
    }

    /** @test */
    public function should_where_return_valid_query()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $value               = '213123123123';
        $key                 = 'user_cpf';
        $queryStringExpected = "user_cpf:213123123123";

        $queryStringReceived = $elasticQueryBuilder->where($key, $value)->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_where_chain_with_where_return_valid_query()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));

        $value  = '12';
        $key    = 'age';
        $value2 = 'New York';
        $key2   = 'city';

        $queryStringExpected = "age:12 AND city:New York";

        $queryStringReceived = $elasticQueryBuilder
            ->where($key, $value)
            ->where($key2, $value2)
            ->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_where_chain_with_where_in_return_valid_query()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));

        $value = '12';
        $key   = 'age';
        $array = ['1111111111', '2222222222'];
        $key2  = 'pointOfSale';

        $queryStringExpected = "age:12 AND pointOfSale:(1111111111 2222222222)";

        $queryStringReceived = $elasticQueryBuilder
            ->where($key, $value)
            ->whereIn($key2, $array)
            ->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_where_in_chain_with_where_return_valid_query()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));

        $value = '12';
        $key   = 'age';
        $array = ['1111111111', '2222222222'];
        $key2  = 'pointOfSale';

        $queryStringExpected = "pointOfSale:(1111111111 2222222222) AND age:12";

        $queryStringReceived = $elasticQueryBuilder
            ->whereIn($key2, $array)
            ->where($key, $value)
            ->toStringQuery();

        $this->assertEquals($queryStringExpected, $queryStringReceived);
    }

    /** @test */
    public function should_return_valid_query_sort_by_desc()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $elasticQueryBuilder->sort('FIELD');

        $sort = [['FIELD' => ['order' => 'desc']]];

        $this->assertEquals($sort, $elasticQueryBuilder->asArray()['body']['sort']);

    }

    /** @test */
    public function should_return_valid_query_from()
    {
        $elasticQueryBuilder = new ElasticsearchQueryBuilder(app()->make(ElasticConnection::class));
        $elasticQueryBuilder->from(23);

        $from = 23;

        $this->assertEquals($from, $elasticQueryBuilder->asArray()['from']);

    }
}
