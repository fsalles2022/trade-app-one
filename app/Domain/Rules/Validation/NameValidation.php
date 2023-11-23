<?php

namespace TradeAppOne\Domain\Rules\Validation;

use TradeAppOne\Domain\Rules\Validation\BaseValidation;
use TradeAppOne\Domain\Rules\Validation\Validation;

final class NameValidation extends BaseValidation
{
    public const KEY = 'name';

    public const WITHOUT_SPECIAL_CHARACTERS_AND_NUMBERS = '/^[^0-9.*,?!@#$%&*-_=+(){};:]+$/';

    public function passes($value): bool
    {
        if (! is_scalar($value)) {
            return false;
        }

        $valid = preg_match(self::WITHOUT_SPECIAL_CHARACTERS_AND_NUMBERS, strtolower($value));

        return (bool) $valid;
    }

    public function getKey(): string
    {
        return self::KEY;
    }
}
