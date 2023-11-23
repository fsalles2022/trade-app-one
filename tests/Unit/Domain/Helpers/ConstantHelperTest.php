<?php

namespace TradeAppOne\Tests\Unit\Domain\Helpers;

use PHPUnit\Framework\TestCase;
use ReflectionException;
use TradeAppOne\Domain\Components\Helpers\ConstantHelper;
use TradeAppOne\Domain\Enumerators\Modes;

class ConstantHelperTest extends TestCase
{

    /** @test */
    public function should_get_all_constants_throw_reflection_exception_when_class_not_exits()
    {
        $this->expectException(ReflectionException::class);
        ConstantHelper::getAllConstants('INVALID_CLASS');
    }

    /** @test */
    public function should_get_value_return_null_when_not_exists()
    {
        $result = ConstantHelper::getValue(Modes::class, 'INVALID_KEY');
        $this->assertNull($result);
    }

    /** @test */
    public function should_get_group_of_values_return_null_when_not_exists()
    {
        $result = ConstantHelper::getGroupOfValues(Modes::class, 'INVALID_KEY');
        $this->assertNull($result);
    }
}