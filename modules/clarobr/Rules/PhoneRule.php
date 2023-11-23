<?php


namespace ClaroBR\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneRule implements Rule
{

    public function passes($attribute, $value): bool
    {
        $isInt  = is_integer((int) $value);
        $length = strlen((int) $value);

        if ($isInt && ($length >= 9 && $length <= 11)) {
            return true;
        }
        return false;
    }

    public function message(): string
    {
        return 'O campo :attribute não se categoriza como telefone.';
    }
}
