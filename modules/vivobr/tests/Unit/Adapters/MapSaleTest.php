<?php

namespace VivoBR\Tests\Unit\Adapters;

use TradeAppOne\Tests\TestCase;
use VivoBR\Adapters\MapSale;

class MapSaleTest extends TestCase
{
    /** @test */
    public function should_return_birthday_null_when_date_invalid()
    {
        $sale = [
            'pessoa' => [
                'dataNascimento' => '0000-00-00'
            ],
            'servicos' => [
                [1]
            ]
        ];

        $map = MapSale::mapOne(null, null, [], $sale);
        $this->assertEquals(false, isset($map[0]['service_customer_birthday']));
    }
}
