<?php

declare(strict_types=1);

namespace TimBR\Enumerators;

class TimBRPackages
{
    // Types
    public const TIM_BLACK_5G_PLUS_TYPE = 'TIM_BLACK_5G_PLUS';

    // External Identifiers
    public const EXTERNAL_IDENTIFIER_BLACK_5G_PLUS = '1-1JUNUD6';

    public const AVAILABLE_PACKAGES = [
        self::EXTERNAL_IDENTIFIER_BLACK_5G_PLUS,
    ];

    public const EXTERNAL_IDENTIFIERS_BY_TYPE = [
        self::TIM_BLACK_5G_PLUS_TYPE => self::EXTERNAL_IDENTIFIER_BLACK_5G_PLUS,
    ];
}
