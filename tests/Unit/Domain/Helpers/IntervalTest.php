<?php

namespace TradeAppOne\Tests\Unit\Domain\Helpers;

use PHPUnit\Framework\TestCase;
use TradeAppOne\Domain\Components\Helpers\Interval;
use TradeAppOne\Domain\Components\Helpers\Period;

class IntervalTest extends TestCase
{
    
   /** @test */
   public function should_create_from_string_return_period()
   {
       $result = Interval::createFromString('day');

       $this->assertEquals(Period::class, get_class($result));
   }

    /** @test */
    public function should_create_from_string_when_not_exists_return_period_with_null()
    {
        $result = Interval::createFromString('batata');

        $this->assertNull($result->initialDate);
        $this->assertNull($result->finalDate);
    }

    /** @test */
    public function should_create_from_string_return_period_with_month_return_non_empty()
    {
        $result = Interval::createFromString('month');

        $this->assertNotNull($result->initialDate);
        $this->assertNotNull($result->finalDate);
    }

    /** @test */
    public function should_create_from_string_return_period_with_week_return_non_empty()
    {
        $result = Interval::createFromString('week');

        $this->assertNotNull($result->initialDate);
        $this->assertNotNull($result->finalDate);
    }

    /** @test */
    public function should_create_from_string_return_period_with_all_return_non_empty()
    {
        $result = Interval::createFromString('all');

        $this->assertNotNull($result->initialDate);
        $this->assertNotNull($result->finalDate);
    }
}
