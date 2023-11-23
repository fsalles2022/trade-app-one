<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules;

use TradeAppOne\Domain\Rules\CpfValidationRule;
use TradeAppOne\Tests\TestCase;

class CpfValidatorTest extends TestCase
{

    protected $cpfValidationRule;

    protected function setUp()
    {
        $this->cpfValidationRule = new CpfValidationRule;
    }

    /** @test */
    public function should_return_boolean_when_passes_function_is_called()
    {
        $returnedValue = $this->cpfValidationRule->passes('cpf', '');

        $this->assertTrue(is_bool($returnedValue));
    }

    /** @test */
    public function should_return_true_when_cpf_is_valid()
    {
        $cpf = '76574362393';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_mask_has_dots_and_dashes()
    {
        $cpf = '765.743.623-93';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_mask_has_commas_and_underscores()
    {
        $cpf = '765,743,623_93';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_lower_than_eleven_chars()
    {
        $cpf = '7657436239';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_greater_than_eleven_chars()
    {
        $cpf = '765743623933333333333333333333333333';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_zeros()
    {
        $cpf = '000000000000';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_ones()
    {
        $cpf = '11111111111';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_two()
    {
        $cpf = '22222222222';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_three()
    {
        $cpf = '33333333333';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_four()
    {
        $cpf = '44444444444';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_five()
    {
        $cpf = '55555555555';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_six()
    {
        $cpf = '666666666666';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_seven()
    {
        $cpf = '77777777777';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_eight()
    {
        $cpf = '88888888888';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_filled_by_nine()
    {
        $cpf = '99999999999';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }
    /** @test */
    public function should_return_false_when_cpf_is_blank()
    {
        $cpf = '';

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_cpf_is_null()
    {
        $cpf = null;

        $isValid = $this->cpfValidationRule->passes('cpf', $cpf);

        $this->assertFalse($isValid);
    }
}
