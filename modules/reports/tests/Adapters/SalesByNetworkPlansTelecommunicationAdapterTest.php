<?php

namespace Reports\Tests\Adapters;

use Reports\Adapters\QueryResults\SalesByNetworkPlansTelecommunicationAdapter;
use Reports\Tests\Fixture\ElasticSearchSalesByNetworkPlansTelecommunicationFixture;
use TradeAppOne\Domain\Enumerators\GroupOfOperations;
use TradeAppOne\Tests\TestCase;

class SalesByNetworkPlansTelecommunicationAdapterTest extends TestCase
{
    /** @test */
    public function should_return_adapter_array()
    {
        $fixture = ElasticSearchSalesByNetworkPlansTelecommunicationFixture::getSaleArray();
        $adapted = SalesByNetworkPlansTelecommunicationAdapter::adapt((collect($fixture)));
        $this->assertEquals($this->mockedReturnAdpater(), $adapted);
    }

    private function mockedReturnAdpater()
    {
        return array(
            'networks' =>
                array(
                    0 => 'Riachuelo',
                    1 => 'Pernambucanas',
                    2 => 'Cea Modas Ltda',
                    3 => 'Lebes',
                    4 => 'Taqi',
                    5 => 'Iplace',
                ),
            'data' => array(
                array(
                    'color' => 'rgba(97, 167, 255, .8)',
                    'name' => trans('constants.group_of_operations.' . GroupOfOperations::PRE_PAGO),
                    'data' => [8065, 6300, 2571, 598, 133, 2]
                ),
                array(
                    'color' => 'rgba(20, 90, 255, .8)',
                    'name' => trans('constants.group_of_operations.' . GroupOfOperations::POS_PAGO),
                    'data' => [38546, 18952, 8584, 652, 573, 465]
                )
            )
        );
    }
}
