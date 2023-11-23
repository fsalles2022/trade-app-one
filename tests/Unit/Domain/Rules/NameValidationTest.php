<?php

namespace TradeAppOne\Tests\Unit\Domain\Rules;

use TradeAppOne\Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use TradeAppOne\Domain\Rules\Validation\NameValidation;

class NameValidationTest extends TestCase
{
    /** @test */
    public function should_passes_return_true_when_name_is_valid()
    {
        $this->assertTrue(is_bool((new NameValidation())->passes('Maria carla')));
    }

    /** @test */
    public function should_passes_return_false_when_name_has_numbers()
    {
        $this->assertFalse((new NameValidation())->passes('maria0'));
    }

    /** @test */
    public function should_passes_return_false_when_name_has_special_characters()
    {
        $this->assertFalse((new NameValidation())->passes('Maria#@$'));
    }

    /** @test */
    public function should_passes_return_false_when_name_is_null()
    {
        $this->assertFalse((new NameValidation())->passes(null));
    }

    /** @test */
    public function should_passes_return_false_when_name_is_empty()
    {
        $this->assertFalse((new NameValidation())->passes(''));
    }

    /** @test */
    public function should_passes_return_false_when_name_is_number()
    {
        $this->assertFalse((new NameValidation())->passes(343));
    }

    /** @test */
    public function should_passes_return_false_when_name_is_object()
    {
        $obj = new \Illuminate\Support\Collection();
        $this->assertFalse((new NameValidation())->passes($obj));
    }

    /** @test */
    public function should_passes_return_false_when_name_is_boolean()
    {
        $this->assertFalse((new NameValidation())->passes(true));
    }

    /** @test */
    public function should_passes_return_false_when_name_is_empty_array()
    {
        $this->assertFalse((new NameValidation())->passes([]));
    }

    /** @test */
    public function should_passes_from_array_return_true_when_name_is_array()
    {
        $this->assertTrue((new NameValidation())->passesValidationProvider(['name' => 'Maria'], 'maria'));
    }

    /** @test */
    public function should_passes_from_array_return_true_when_array_is_empty_and_value_is_valid()
    {
        $this->assertTrue((new NameValidation())->passesValidationProvider(null, 'maria'));
    }

    /** @test */
    public function should_passes_from_array_return_false_when_array_is_empty_and_value_is_valid()
    {
        $this->assertFalse((new NameValidation())->passesValidationProvider(null, '34maria'));
    }

    /** @test */
    public function should_return_true_when_called_from_custom_validation()
    {
        $validator = Validator::make(['name' => 'Maria Carla'], ['name' => 'name']);
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function should_return_false_when_called_from_custom_validation()
    {
        $validator = Validator::make(['name' => '3234'], ['name' => 'name']);
        $this->assertNotTrue($validator->passes());
    }
}