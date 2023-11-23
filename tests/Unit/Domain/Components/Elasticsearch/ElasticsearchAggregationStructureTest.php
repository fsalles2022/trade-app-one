<?php

namespace TradeAppOne\Tests\Unit\Domain\Components\Elasticsearch;

use TradeAppOne\Domain\Components\Elasticsearch\ElasticsearchAggregationStructure;
use TradeAppOne\Tests\TestCase;

class ElasticsearchAggregationStructureTest extends TestCase
{
    /**
     * @var ElasticsearchAggregationStructure
     */
    private $aggregationBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->aggregationBuilder = new ElasticsearchAggregationStructure('key');
    }

    /**
     * @test
     */
    public function terms()
    {
        $this->aggregationBuilder->terms('term');
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('term', $builderAsArray['key']['terms']['field']);
    }

    /**
     * @test
     */
    public function sum()
    {
        $this->aggregationBuilder->sum('sum');
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('sum', $builderAsArray['key']['sum']['field']);
    }

    /**
     * @test
     */
    public function valueCount()
    {
        $this->aggregationBuilder->valueCount('valueCount');
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('valueCount', $builderAsArray['key']['value_count']['field']);
    }

    /**
     * @test
     */
    public function range()
    {
        $this->aggregationBuilder->range('range', ['ranges' => [['from' => 10]]]);
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('range', $builderAsArray['key']['range']['field']);
        $this->assertNotEmpty($builderAsArray['key']['range']['ranges']);
    }

    /**
     * @test
     */
    public function cardinality()
    {
        $this->aggregationBuilder->cardinality('cardinality');
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('cardinality', $builderAsArray['key']['cardinality']['field']);
    }

    /**
     * @test
     */
    public function filterTermByFieldValue()
    {
        $this->aggregationBuilder->filterTermByFieldValue('test_field', 'value');
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('value', $builderAsArray['key']['filter']['terms']['test_field']);
    }

    /**
     * @test
     */
    public function filterExists()
    {
        $this->aggregationBuilder->filterExists('test_field');
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('test_field', $builderAsArray['key']['filter']['exists']['field']);
    }

    /**
     * @test
     */
    public function dateRange()
    {
        $this->aggregationBuilder->dateRange(
            'test_field',
            ['format' => 'yyyy-mm-dd', 'ranges' => [['to' => '2018-04-04']]]
        );
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('test_field', $builderAsArray['key']['date_range']['field']);
        $this->assertNotEmpty($builderAsArray['key']['date_range']['ranges']);
    }

    /**
     * @test
     */
    public function dateHistogram()
    {
        $this->aggregationBuilder->dateHistogram('test_field', ['format' => 'yyyy-mm-dd', 'interval' => 'day']);
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertEquals('test_field', $builderAsArray['key']['date_histogram']['field']);
        $this->assertEquals('day', $builderAsArray['key']['date_histogram']['interval']);
    }

    /**
     * @test
     */
    public function nest()
    {
        $nest = new ElasticsearchAggregationStructure('nest');
        $nest->terms('test_field');

        $this->aggregationBuilder->nest($nest);
        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertArrayHasKey('nest', $builderAsArray['key']['aggs']);
        $this->assertArrayHasKey('terms', $builderAsArray['key']['aggs']['nest']);
    }

    /**
     * @test
     */
    public function brother()
    {
        $firstBrother  = new ElasticsearchAggregationStructure('firstBrother');
        $secondBrother = new ElasticsearchAggregationStructure('secondBrother');

        $firstBrother->terms('test_field');
        $secondBrother->terms('test_field');

        $this->aggregationBuilder->brother($firstBrother);
        $this->aggregationBuilder->brother($secondBrother);

        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertArrayHasKey('firstBrother', $builderAsArray);
        $this->assertArrayHasKey('secondBrother', $builderAsArray);

        $this->assertNotEmpty($builderAsArray['firstBrother']);
        $this->assertNotEmpty($builderAsArray['secondBrother']);
    }

    /**
     * @test
     */
    public function brothers_()
    {
        $firstBrother  = new ElasticsearchAggregationStructure('firstBrother');
        $secondBrother = new ElasticsearchAggregationStructure('secondBrother');
        $threeBrother  = new ElasticsearchAggregationStructure('threeBrother');

        $firstBrother->terms('test_field');
        $secondBrother->terms('test_field');
        $threeBrother->terms('test_field');

        $arrayBrother = [$firstBrother, $secondBrother, $threeBrother];
        $this->aggregationBuilder->brothers($arrayBrother);

        $builderAsArray = $this->aggregationBuilder->asArray();

        $this->assertArrayHasKey('firstBrother', $builderAsArray);
        $this->assertArrayHasKey('secondBrother', $builderAsArray);
        $this->assertArrayHasKey('threeBrother', $builderAsArray);

        $this->assertNotEmpty($builderAsArray['firstBrother']);
        $this->assertNotEmpty($builderAsArray['secondBrother']);
        $this->assertNotEmpty($builderAsArray['threeBrother']);
    }
}
