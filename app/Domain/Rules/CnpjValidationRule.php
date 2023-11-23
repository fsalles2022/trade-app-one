<?php


namespace TradeAppOne\Domain\Rules;

use Illuminate\Validation\Rule;

class CnpjValidationRule extends Rule
{

    private static $invalidCases = [
        '00000000000000',
        '11111111111111',
        '22222222222222',
        '33333333333333',
        '44444444444444',
        '55555555555555',
        '66666666666666',
        '77777777777777',
        '88888888888888',
        '99999999999999'
    ];

    public function passes($attribute, $value)
    {
        if ($this->isInvalid($value)) {
            return false;
        }

        $c = preg_replace('/\D/', '', $value);
        if (strlen($c) != 14 || preg_match("/^{$c[0]}{14}$/", $c)) {
            return false;
        }
        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]) {
            ;
        }

        if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
        for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]) {
            ;
        }
        if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
        return true;
    }

    private function isInvalid($cnpj)
    {
        return $this->hasInvalidSize($cnpj) || $this->hasSequencialNumbers($cnpj);
    }

    private function hasInvalidSize($cnpj)
    {
        return empty($cnpj) || strlen($cnpj) <> 14;
    }

    private function hasSequencialNumbers($cnpj)
    {
        return in_array($cnpj, $this::$invalidCases);
    }

    public function message()
    {
        return trans('validation.cnpj');
    }
}
