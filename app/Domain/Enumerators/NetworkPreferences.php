<?php

namespace TradeAppOne\Domain\Enumerators;

final class NetworkPreferences
{
    const INITIAL_PAGE   = 'INITIAL_PAGE';
    const SHUFFLE_BANNER = 'BANNER_SHUFFLE';
    const PREFERENCES    = [self::INITIAL_PAGE, self::SHUFFLE_BANNER];
}
