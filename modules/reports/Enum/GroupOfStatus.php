<?php

namespace Reports\Enum;

use TradeAppOne\Domain\Enumerators\ServiceStatus;

final class GroupOfStatus
{
    const VALID_SALES = [
        ServiceStatus::ACCEPTED,
        ServiceStatus::APPROVED,
    ];

    const PERFORMED_SALES = [
        ServiceStatus::ACCEPTED,
        ServiceStatus::APPROVED,
        ServiceStatus::CANCELED
    ];
}
