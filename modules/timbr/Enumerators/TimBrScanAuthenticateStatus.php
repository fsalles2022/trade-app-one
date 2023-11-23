<?php

declare(strict_types=1);

namespace TimBR\Enumerators;

class TimBrScanAuthenticateStatus
{
    public const NOT_INITIALIZED_STATUS     = 'NOT_INITIALIZED';
    public const IN_PROGRESS_STATUS         = 'IN_PROGRESS';
    public const SUCCESS_STATUS             = 'SUCCESS';
    public const DOCUMENT_WITH_RISC_STATUS  = 'DOCUMENT_WITH_RISC';
    public const MISSING_DOCUMENT_STATUS    = 'MISSING_DOCUMENT';
    public const WITH_RISC_STATUS           = 'WITH_RISC';
    public const ILLEGIBLE_DOCUMENT_STATUS  = 'ILLEGIBLE_DOCUMENT';
    public const INCOMPLETE_DOCUMENT_STATUS = 'INCOMPLETE_DOCUMENT';

    // BrScan API Status
    public const BR_SCAN_NOT_INITIALIZED     = 'analise-de-biometria-nao-iniciada';
    public const BR_SCAN_IN_PROGRESS         = 'analise-de-biometria-em-andamento';
    public const BR_SCAN_SUCCESS             = 'sem-risco-aparente';
    public const BR_SCAN_DOCUMENT_WITH_RISC  = 'com-risco-no-documento-de-identificacao';
    public const BR_SCAN_WITH_RISC           = 'com-risco-aparente';
    public const BR_SCAN_MISSING_DOCUMENT    = 'documento-de-identificacao-ausente';
    public const BR_SCAN_ILLEGIBLE_DOCUMENT  = 'documento-de-identificacao-ilegivel';
    public const BR_SCAN_INCOMPLETE_DOCUMENT = 'documento-de-identificacao-incompleto';

    public const DESCRIPTION = [
        self::BR_SCAN_NOT_INITIALIZED       => self::NOT_INITIALIZED_STATUS,
        self::BR_SCAN_IN_PROGRESS           => self::IN_PROGRESS_STATUS,
        self::BR_SCAN_SUCCESS               => self::SUCCESS_STATUS,
        self::BR_SCAN_DOCUMENT_WITH_RISC    => self::DOCUMENT_WITH_RISC_STATUS,
        self::BR_SCAN_WITH_RISC             => self::WITH_RISC_STATUS,
        self::BR_SCAN_MISSING_DOCUMENT      => self::MISSING_DOCUMENT_STATUS,
        self::BR_SCAN_ILLEGIBLE_DOCUMENT    => self::ILLEGIBLE_DOCUMENT_STATUS,
        self::BR_SCAN_INCOMPLETE_DOCUMENT   => self::INCOMPLETE_DOCUMENT_STATUS,
    ];

    public static function getTransformedStatus(string $brScanStatus): ?string
    {
        return self::DESCRIPTION[str_slug($brScanStatus)] ?? null;
    }
}
