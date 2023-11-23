<?php

namespace TradeAppOne\Domain\Components\Helpers;

class BankDataHelper
{
    public static function getVerificationDigit($account): int
    {
        $digit = substr($account, -1);
        return intval($digit);
    }

    public static function removeVerifyingDigit($account): int
    {
        $accountLen      = strlen($account)-1;
        $checkingAccount = substr($account, 0, $accountLen);

        return intval($checkingAccount);
    }

    public static function composeAccount($account, $operation = null): int
    {
        $account = self::removeVerifyingDigit($account);

        if (is_numeric($operation)) {
            $account = $account . $operation;
        }

        return intval($account);
    }
}
