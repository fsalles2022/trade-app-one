<?php

namespace TradeAppOne\Domain\Rules;

use Illuminate\Validation\Rule;

class PasswordValidationRule extends Rule
{

    private static $passwordRegex = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{6,}$/";

    private static $sequentialRegex = "
        /^(?!.*?(
        abc|bcd|cde|def|efg|fgh|ghi|hij|
        ijk|jkl|klm|lmn|mno|nop|opq|pqr|
        qrs|rst|stu|tuv|uvw|vwx|wxy|xyz|
        012|123|234|345|456|567|678|789
        )).*/";

    private static $reverseSequentialRegex = "
        /^(?!.*?(
        cba|dcb|edc|fed|gfe|hgf|ihg|jih|
        kji|lkj|mlk|nml|onm|pon|pqo|rqp|
        srq|tsr|uts|vut|wvu|xwv|yxw|zyx|
        210|321|432|543|654|765|876|987
        )).*/";

    public function passes($attribute, $value)
    {
        if ($this->hasNotSequentialChars($value)
            && $this->hasNotInvalidChars($value)
            && $this->hasNotReverseSequentialChars($value)) {
            return true;
        }

        return false;
    }

    private function hasNotSequentialChars($password)
    {
        return preg_match($this::$sequentialRegex, strtolower($password));
    }

    private function hasNotReverseSequentialChars($password)
    {
        return preg_match($this::$reverseSequentialRegex, strtolower($password));
    }

    private function hasNotInvalidChars($password)
    {
        return preg_match($this::$passwordRegex, $password);
    }

    public function message()
    {
        return trans('validation.password');
    }
}
