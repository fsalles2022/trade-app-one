<?php

declare(strict_types=1);

namespace TimBR\Enumerators;

class TimBrScanSaleTermStatus
{
    public const IN_PROGRESS_STATUS = 'IN_PROGRESS';
    public const SUCCESS_STATUS     = 'SUCCESS';
    public const REJECTED_STATUS    = 'REJECTED';

    // BrScan API Status
    public const BR_SCAN_IN_PROGRESS = 'pendente-de-aceite-do-usuario';
    public const BR_SCAN_SUCCESS     = 'aceite-da-contratacao-realizado';
    public const BR_SCAN_REJECTED    = 'contratacao-negada';

    public const DESCRIPTION = [
        self::BR_SCAN_IN_PROGRESS => self::IN_PROGRESS_STATUS,
        self::BR_SCAN_SUCCESS     => self::SUCCESS_STATUS,
        self::BR_SCAN_REJECTED    => self::REJECTED_STATUS,
    ];

    public static function getTransformedStatus(string $brScanStatus): ?string
    {
        return self::DESCRIPTION[str_slug($brScanStatus)] ?? null;
    }
}
