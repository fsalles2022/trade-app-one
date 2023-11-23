<?php

namespace TradeAppOne\Domain\Rules\Validation;

abstract class BaseValidation implements Validation
{
    protected const KEY = 'validation';

    abstract public function passes($value): bool;

    abstract public function getKey(): string;

    public function passesValidationProvider($attribute, $value): bool
    {
        if (is_array($value) && isset($value[$attribute])) {
            return $this->passes($value[$attribute]);
        }

        return $this->passes($value);
    }

    public function fails($value): bool
    {
        return ! $this->passes($value);
    }

    public function message(): string
    {
        return trans('validation.'.$this->getKey());
    }
}
