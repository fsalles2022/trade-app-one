<?php

declare(strict_types=1);

namespace TimBR\Enumerators;

class TimBRDefaultPackages
{
    private const CONTROLE_PROMO_20GB = '1-1E5NB0X';
    private const EXPRESS_PROMO_20GB  = '1-1E90L5J';

    public const AVAILABLE_PACKAGES = [
        self::CONTROLE_PROMO_20GB,
        self::EXPRESS_PROMO_20GB,
    ];
}
