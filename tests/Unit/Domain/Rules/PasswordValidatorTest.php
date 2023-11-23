<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules;

use TradeAppOne\Domain\Rules\PasswordValidationRule;
use TradeAppOne\Tests\TestCase;

class PasswordValidatorTest extends TestCase
{

    protected $passwordValidationRule;

    protected function setUp()
    {
        $this->passwordValidationRule = new PasswordValidationRule;
    }

    /** @test */
    public function should_return_boolean_when_passes_is_called()
    {
        $returnedValue = $this->passwordValidationRule->passes('password', '');

        $this->assertTrue(is_bool($returnedValue));
    }

    /** @test */
    public function should_return_true_when_password_is_6chars_1lower_letter_1upper_letter_1number_1special_char_valid()
    {
        $password = 'Omeg@3';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_lower_than_6_chars()
    {
        $password = 'Om3g@';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_only_numbers()
    {
        $password = '135790';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_only_lower_case_letters()
    {
        $password = 'acegik';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_only_uppercase_letters()
    {
        $password = 'ACEGIK';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_only_repeated_numbers()
    {
        $password = '111111';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_only_repeated_lowercase_letters()
    {
        $password = 'aaaaaa';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_only_repeated_uppercase_letters()
    {
        $password = 'AAAAAA';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_sequential_lowercase_letters()
    {
        $password = 'abcdef';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_sequential_uppercase_letters()
    {
        $password = 'ABCDEF';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_reverse_sequential_lowercase_letters()
    {
        $password = 'dcba@1';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_reverse_sequential_uppercase_letters()
    {
        $password = 'DCB@1';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_sequential_numbers()
    {
        $password = '123456';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_reverse_sequential_numbers()
    {
        $password = '678@Casa';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_sequential_chars_and_numbers_invalid()
    {
        $password = '@Bcd012';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_reverse_sequential_chars_and_numbers_invalid()
    {
        $password = '@Nml765';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_blank()
    {
        $password = '';

        $isValid = $this->passwordValidationRule->passes('password', $password);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function should_return_false_when_password_is_invalid()
    {
        $passowrd = null;

        $isValid = $this->passwordValidationRule->passes('password', $passowrd);

        $this->assertFalse($isValid);
    }
}
