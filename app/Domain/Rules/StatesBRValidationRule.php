<?php

namespace TradeAppOne\Domain\Rules;

use Illuminate\Validation\Rule;

class StatesBRValidationRule extends Rule
{
    private static $states = [
        'AC',
        'AL',
        'AM',
        'AP',
        'BA',
        'CE',
        'DF',
        'ES',
        'GO',
        'MA',
        'MG',
        'MS',
        'MT',
        'PA',
        'PB',
        'PE',
        'PI',
        'PR',
        'RJ',
        'RN',
        'RO',
        'RR',
        'RS',
        'SC',
        'SE',
        'SP',
        'TO'
    ];

    public function passes($attribute, $value)
    {
        if (in_array($value, $this::$states)) {
            return true;
        }

        return false;
    }

    public function message()
    {
        return trans('validation.states_br');
    }
}
