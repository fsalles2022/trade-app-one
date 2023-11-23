<?php

namespace TradeAppOne\Domain\Rules;

use Illuminate\Validation\Rule;

class CpfValidationRule extends Rule
{

    private static $invalidCases = [
        '00000000000',
        '11111111111',
        '22222222222',
        '33333333333',
        '44444444444',
        '55555555555',
        '66666666666',
        '77777777777',
        '88888888888',
        '99999999999'
    ];

    public function passes($attribute, $value)
    {
        if ($this->isInvalid($value)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $value{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($value{$c} != $d) {
                return false;
            }

            return true;
        }
    }

    private function isInvalid($cpf)
    {
        return $this->hasInvalidSize($cpf) || $this->hasSequencialNumbers($cpf);
    }

    private function hasInvalidSize($cpf)
    {
        return empty($cpf) || strlen($cpf) <> 11;
    }

    private function hasSequencialNumbers($cpf)
    {
        return in_array($cpf, $this::$invalidCases);
    }

    public function message()
    {
        return trans('validation.cpf');
    }
}
