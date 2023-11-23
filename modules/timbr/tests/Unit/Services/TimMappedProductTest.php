<?php

namespace TimBR\Tests\Unit\Services;

use TimBR\Services\TimMappedProduct;
use TradeAppOne\Tests\TestCase;

class TimMappedProductTest extends TestCase
{
    /** @test */
    public function should_return_plan_without_loyalty()
    {
        $products = collect([
            ['product' => '1-118LLUU', 'price' => 79.99]
        ]);

        $service['product'] = '1-118LLUU';
        $expectedPrice      = 79.99;

        $price = TimMappedProduct::getPrice($products, $service);

        $this->assertEquals($price, $expectedPrice);
    }

    /** @test */
    public function should_return_plan_loyalty()
    {
        $products = collect([
            ['product' => '1-118LLUU', 'price' => 79.99, 'loyalt' => "1-11T8CVP"]
        ]);

        $service['product'] = '1-118LLUU';
        $expectedPrice      = 79.99;

        $price = TimMappedProduct::getPrice($products, $service);

        $this->assertEquals($price, $expectedPrice);
    }

    /** @test */
    public function should_return_plan_loyalty_correct()
    {
        $products = collect([
            [
                'product' => '1-118LLUU',
                'price' => 64.99,
                'loyalty' => ['id' => "1-11T8CVP"]
            ],
            [
                'product' => "1-IL65OW",
                'price' => 49.99,
                'loyalty' => ['id' => "1-11T8CVP"]
            ]
        ]);

        $service['product'] = "1-IL65OW";
        $service['loyalty'] = ['id' => "1-11T8CVP"];
        $expectedPrice      = 49.99;

        $price = TimMappedProduct::getPrice($products, $service);

        $this->assertEquals($price, $expectedPrice);
    }
}
