<?php

namespace TradeAppOne\Tests\Unit\Domain\Components\Console;

use TradeAppOne\Domain\Components\Console\OptionsValidator;
use TradeAppOne\Tests\TestCase;

class OptionsValidatorTest extends TestCase
{
    /** @test */
    public function should_split_array_options()
    {
        $options = ['op1' => ['a', 'b'], 'op2' => true];
        $result  = OptionsValidator::validate($options, ['op1' => 'string', 'op2' => 'boolean']);
        self::assertTrue($result);
    }
}
