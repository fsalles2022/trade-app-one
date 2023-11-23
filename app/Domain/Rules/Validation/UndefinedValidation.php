<?php

namespace TradeAppOne\Domain\Rules\Validation;

use Illuminate\Support\Str;
use TradeAppOne\Domain\Rules\Validation\BaseValidation;
use TradeAppOne\Domain\Rules\Validation\Validation;

class UndefinedValidation extends BaseValidation
{
    public const KEY = 'without_undefined';

    public function passes($value): bool
    {
        if (! is_scalar($value)) {
            return false;
        }

        $isUndefined = Str::is($value, 'undefined');

        if ($isUndefined) {
            return false;
        }

        return true;
    }

    public function getKey(): string
    {
        return self::KEY;
    }
}
