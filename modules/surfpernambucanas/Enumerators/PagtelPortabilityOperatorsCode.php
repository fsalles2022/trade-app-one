<?php

declare(strict_types=1);

namespace SurfPernambucanas\Enumerators;

use TradeAppOne\Domain\Enumerators\Operations;

final class PagtelPortabilityOperatorsCode
{
    /** @var string[] */
    public const PORTABILITY_CODES = [
        '0341' => Operations::TIM,
        '0321' => Operations::CLARO,
        '0320' => Operations::VIVO,
        '0331' => Operations::OI,
        '0351' => Operations::NEXTEL
    ];
}
