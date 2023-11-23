<?php

declare(strict_types=1);

namespace Outsourced\CasaEVideo\Connection;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

final class CasaEVideoHeaders
{
    private const CONFIG = 'outsourced.'. NetworkEnum::CASAEVIDEO;

    public static function getUri(): string
    {
        return config(self::CONFIG . '.uri');
    }
}
