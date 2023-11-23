<?php


namespace Outsourced\ViaVarejo\Enumerators;

use TradeAppOne\Domain\Enumerators\ServiceStatus;

class ViaVarejoStatus
{
    public const AC = 'AC';
    public const AP = 'AP';
    public const RJ = 'RJ';
    public const CA = 'CA';
    public const SM = 'SM';
    public const PS = 'PS';

    public const OPTIONS = [
        ServiceStatus::APPROVED => self::AP,
        ServiceStatus::ACCEPTED => self::AC,
        ServiceStatus::REJECTED => self::RJ,
        ServiceStatus::CANCELED => self::CA,
        ServiceStatus::SUBMITTED => self::SM,
        ServiceStatus::PENDING_SUBMISSION => self::PS
    ];

    public static function get(string $status): string
    {
        return self::OPTIONS[$status] ?? '';
    }
}
