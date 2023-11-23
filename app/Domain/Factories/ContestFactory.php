<?php

namespace TradeAppOne\Domain\Factories;

use ClaroBR\Services\ClaroBRContest;
use TimBR\Services\TimBRContest;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Services\ContestBehavior;
use TradeAppOne\Exceptions\SystemExceptions\ServiceExceptions;
use VivoBR\Services\VivoContest;

final class ContestFactory
{
    const CONTEST_OPERATORS = [Operations::CLARO => ClaroBRContest::class, Operations::VIVO => VivoContest::class, Operations::TIM => TimBRContest::class];

    public static function make(string $operator): ContestBehavior
    {
        try {
            return app()->make(self::CONTEST_OPERATORS[$operator]);
        } catch (\Exception $exception) {
            ServiceExceptions::SERVICE_CANNOT_BE_CONTESTED();
        }
    }
}
