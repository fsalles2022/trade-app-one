<?php

namespace TradeAppOne\Domain\Enumerators;

final class PasswordResetStatus
{
    const WAITING  = 'WAITING';
    const APPROVED = 'APPROVED';
    const REJECTED = 'REJECTED';
}
