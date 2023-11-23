<?php

declare(strict_types=1);

namespace Tradehub\Enumerators;

use TradeAppOne\Domain\Enumerators\ServiceStatus;

class TradeHubStatus
{
    public const APPROVED = 'APPROVED';
    public const CANCELED = 'CANCELED';
    public const PAYMENT_DISAPPROVED = 'PAYMENT_DISAPPROVED';

    public const TRANSLATE = [
        self::APPROVED => ServiceStatus::APPROVED,
        self::CANCELED => ServiceStatus::CANCELED,
        self::PAYMENT_DISAPPROVED => ServiceStatus::REJECTED,
    ];
}
