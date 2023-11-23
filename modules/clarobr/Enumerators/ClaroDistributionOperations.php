<?php

declare(strict_types=1);

namespace ClaroBR\Enumerators;

class ClaroDistributionOperations
{
    public const OUT        = 'OUT';
    public const STRUCTURAL = 'ESTRUTURAL';

    public const AVAILABLE = [
        self::OUT,
        self::STRUCTURAL,
    ];
}
