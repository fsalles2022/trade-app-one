<?php

declare(strict_types=1);

namespace TradeAppOne\Rules;

use Illuminate\Contracts\Validation\Rule;

class GreaterThan implements Rule
{
    /**
     * @var int
     */
    private $min;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $min)
    {
        $this->min = $min;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $value > $this->min;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must be greater than :value, not lesser or equals.';
    }
}
