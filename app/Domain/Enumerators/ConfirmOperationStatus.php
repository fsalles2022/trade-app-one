<?php

namespace TradeAppOne\Domain\Enumerators;

final class ConfirmOperationStatus
{
    const SUCCESS = 'SUCCESS';
    const FAILED  = 'FAILED';
    const STATUS  = [self::SUCCESS, self::FAILED];
}
