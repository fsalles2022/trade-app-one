<?php

namespace Buyback\Enumerators;

use TradeAppOne\Domain\Enumerators\ServiceStatus;

class EvaluationStatus
{
    public const APPRAISER = 'appraiser';
    public const CARRIER   = 'carrier';

    public const EVALUATION_STATUS_BY_TYPE = [
        self::APPRAISER => ServiceStatus::APPROVED,
        self::CARRIER => ServiceStatus::ACCEPTED
    ];
}
