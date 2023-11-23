<?php

namespace TradeAppOne\Tests\Unit\Domain\Components;

use InvalidArgumentException;
use TradeAppOne\Domain\Components\Helpers\ITUE164NumberStructure;
use TradeAppOne\Tests\TestCase;

class ITUE164NumberStructureTest extends TestCase
{
    /** @test */
    public function should_return_exception_invalid_area_code()
    {
        $this->expectException(InvalidArgumentException::class);
        ITUE164NumberStructure::pullAreaCode('30994940047');
    }

    /** @test */
    public function should_return_exception_valid_area_code()
    {
        $areaCode = ITUE164NumberStructure::pullAreaCode('67994940047');
        self::assertEquals('67', $areaCode);
    }

    /** @test */
    public function should_return_exception_invalid_area_code_lower_than_11()
    {
        $this->expectException(InvalidArgumentException::class);
        ITUE164NumberStructure::pullAreaCode('309949400');
    }

    /** @test */
    public function should_return_exception_invalid_area_code_grater_than_15()
    {
        $this->expectException(InvalidArgumentException::class);
        ITUE164NumberStructure::pullAreaCode('309949402130');
    }
}
