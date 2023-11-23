<?php

namespace ClaroBR\Tests\Unit\Components;

use ClaroBR\Components\ClaroBRDomains;
use TradeAppOne\Tests\TestCase;

class ClaroBRDomainsTest extends TestCase
{
    /** @test */
    public function should_return_true_for_valid_code()
    {
        $isValid = ClaroBRDomains::validatePointOfSaleIdentifier('323S');
        self::assertTrue($isValid);
    }

    /** @test */
    public function should_return_false_for_code_with_3_chars()
    {
        $isValid = ClaroBRDomains::validatePointOfSaleIdentifier('32S');
        self::assertFalse($isValid);
    }

    /** @test */
    public function should_return_true_for_code_with_5_chars()
    {
        $isValid = ClaroBRDomains::validatePointOfSaleIdentifier('32Sss');
        self::assertTrue($isValid);
    }

    /** @test */
    public function should_return_false_for_code_with_special_chars()
    {
        $isValid = ClaroBRDomains::validatePointOfSaleIdentifier('32+s');
        self::assertFalse($isValid);
    }

    /** @test */
    public function should_return_true_for_code_only_numbers()
    {
        $isValid = ClaroBRDomains::validatePointOfSaleIdentifier('1234');
        self::assertTrue($isValid);
    }

    /** @test */
    public function should_return_true_for_code_only_letters()
    {
        $isValid = ClaroBRDomains::validatePointOfSaleIdentifier('AAAA');
        self::assertTrue($isValid);
    }

    /** @test */
    public function should_return_code_upper()
    {
        $upperCode = ClaroBRDomains::formatPointOfSaleIdentifier('abcd');
        self::assertTrue(ctype_upper($upperCode));
    }
}
