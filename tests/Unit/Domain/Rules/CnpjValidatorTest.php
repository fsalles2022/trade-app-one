<?php


namespace TradeAppOne\Tests\Unit\Domain\Rules;

use TradeAppOne\Domain\Rules\CnpjValidationRule;
use TradeAppOne\Tests\TestCase;

class CnpjValidatorTest extends TestCase
{

    protected $cnpjValidationRule;

    protected function setUp()
    {
        $this->cnpjValidationRule = new CnpjValidationRule;
    }

    //** @test */
    public function should_return_boolean_when_passes_function_is_called()
    {
        $returnValue = $this->cnpjValidationRule->passes('cnpj', '');

        $this->assertTrue(is_bool($returnedValue));
    }

    /** @test */
    public function should_return_true_when_cnpj_is_valid()
    {
        $cnpj = '35294673000186';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_lower_then_fouteen_chars()
    {
        $cnpj = '7657436239';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_greater_than_fourteen_chars()
    {
        $cnpj = '765743623933333333333333333333333333';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_invalid()
    {

        $cnpj = '35294672000686';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_zeroes()
    {
        $cnpj = '00000000000000';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_ones()
    {
        $cnpj = '11111111111111';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_two()
    {
        $cnpj = '22222222222222';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_three()
    {
        $cnpj = '33333333333333';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_four()
    {
        $cnpj = '44444444444444';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_five()
    {
        $cnpj = '55555555555555';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_six()
    {
        $cnpj = '66666666666666';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_seven()
    {
        $cnpj = '77777777777777';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_eight()
    {
        $cnpj = '88888888888888';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_filled_by_nine()
    {
        $cnpj = '99999999999999';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_blank()
    {
        $cnpj = '';

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cnpj_is_null()
    {
        $cnpj = null;

        $isValid = $this->cnpjValidationRule->passes('cnpj', $cnpj);

        $this->assertFalse($isValid);
    }
}
