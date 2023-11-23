<?php

namespace Gateway\Tests\Unit\Components;

use Gateway\Components\Interest;
use TradeAppOne\Tests\TestCase;

class InterestTest extends TestCase
{
    /** @test */
    public function should_apply_correct_interest()
    {
        $price = self::rand_float();

        foreach (Interest::RATES as $times => $percentage) {
            $newPrice = Interest::apply($price, $times);
            $expected = round($price / (1 - $percentage), 2);

            $this->assertEquals($expected, $newPrice);
        }
    }

    /** @test */
    public function should_remove_interest_when_firstFree_is_true_in_apply()
    {
        $price = self::rand_float();

        $priceInterest = Interest::apply($price, 1);

        foreach (Interest::RATES as $times => $percentage) {
            $newPrice = Interest::apply($priceInterest, $times, true);
            $expected = Interest::apply($price, $times);

            $this->assertEquals($expected, $newPrice);
        }
    }

    /** @test */
    public function should_return_removed_interest()
    {
        $price = self::rand_float();
        $times = random_int(1, 12);

        $newPrice = Interest::apply($price, $times);

        $this->assertEquals($price, Interest::remove($newPrice, $times));
    }

    private static function rand_float($st_num = 400, $end_num = 5000, $mul = 1000000)
    {
        $num = random_int($st_num * $mul, $end_num * $mul)/$mul;

        return round($num, 2);
    }
}
