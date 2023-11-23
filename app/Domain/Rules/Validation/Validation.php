<?php

namespace TradeAppOne\Domain\Rules\Validation;

interface Validation
{
    public function passes($value): bool;
    public function passesValidationProvider($attribute, $value): bool;
    public function fails($value): bool;
    public function message(): string;
}
