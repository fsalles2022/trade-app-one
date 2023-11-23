<?php

namespace TradeAppOne\Domain\Enumerators;

final class ServiceStatus
{
    const PENDING_SUBMISSION = 'PENDING_SUBMISSION';
    const SUBMITTED          = 'SUBMITTED';
    const ACCEPTED           = 'ACCEPTED';
    const APPROVED           = 'APPROVED';
    const CANCELED           = 'CANCELED';
    const REJECTED           = 'REJECTED';
}
