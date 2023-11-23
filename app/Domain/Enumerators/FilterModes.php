<?php

namespace TradeAppOne\Domain\Enumerators;

class FilterModes
{
    const ALL    = 'ALL';
    const CHOSEN = 'CHOSEN';

    const AVAILABLE = [
        self::ALL,
        self::CHOSEN
    ];
}
