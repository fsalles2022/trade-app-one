<?php


namespace TradeAppOne\Domain\Services\Update;

use ClaroBR\Services\UpdateAttributes\ClaroBRUpdateImeiService;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Exceptions\BusinessExceptions\OperatorNoExists;

class SaleUpdateFactory
{
    private static $AVAILABLE_OPERATORS = [
        Operations::CLARO => ClaroBRUpdateImeiService::class
    ];

    public static function make(string $operator)
    {
        if (array_key_exists($operator, self::$AVAILABLE_OPERATORS)) {
            return resolve(self::$AVAILABLE_OPERATORS[$operator]);
        }
        throw new OperatorNoExists($operator);
    }
}
