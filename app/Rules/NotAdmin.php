<?php

declare(strict_types=1);

namespace TradeAppOne\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotAdmin implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return strtoupper($value) !== 'ADMINISTRATOR';
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "The field :attribute cannot has 'ADMINISTRATOR' as an acceptable value (ACCESS VIOLATION).";
    }
}
