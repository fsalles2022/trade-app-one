<?php

namespace Reports\Tests\Adapters;

use Reports\Adapters\MergePointsOfSaleWithNetworksFilter;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Traits\ArrayAssertTrait;
use TradeAppOne\Tests\TestCase;

class MergePointsOfSaleWithNetworksFilterTest extends TestCase
{
    use ArrayAssertTrait;

    private $hierarchy;
    private $networks;
    private $pointsOfSale;

    protected function setUp()
    {
        parent::setUp();

        $this->networks     = collect()->push((new NetworkBuilder())->build());
        $this->hierarchy    = collect()->push((new HierarchyBuilder())->withNetwork($this->networks->first())->build());
        $this->pointsOfSale = collect()
            ->push((new PointOfSaleBuilder())->withHierarchy($this->hierarchy->first())
            ->withNetwork($this->networks->first())->build());
    }

    /** @test */
    public function should_return_an_instance_when_called(): void
    {
        $mergePointsOfSaleWithNetworksFilter = new MergePointsOfSaleWithNetworksFilter(collect(), collect(), collect());
        $this->assertInstanceOf(MergePointsOfSaleWithNetworksFilter::class, $mergePointsOfSaleWithNetworksFilter);
    }

    /** @test */
    public function should_return_array_when_called_with_the_correct_parameters(): void
    {
        $mergePointsOfSaleWithNetworksFilter = new MergePointsOfSaleWithNetworksFilter($this->networks, $this->hierarchy, $this->pointsOfSale);
        $adapted                             = $mergePointsOfSaleWithNetworksFilter->adapt();

        $this->assertInternalType('array', $adapted);
    }

    /** @test */
    public function should_return_adapted_array_when_called_with_the_correct_parameters(): void
    {

        $mergePointsOfSaleWithNetworksFilter = new MergePointsOfSaleWithNetworksFilter($this->networks, $this->hierarchy, $this->pointsOfSale);
        $adapted                             = $mergePointsOfSaleWithNetworksFilter->adapt();

        $this->assertArrayStructure($adapted, [
            [
                'id',
                'label',
                'hierarchies' => [
                    [
                        'id',
                        'label',
                        'pointsOfSale' => [
                            [
                                'id',
                                'label',
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}
