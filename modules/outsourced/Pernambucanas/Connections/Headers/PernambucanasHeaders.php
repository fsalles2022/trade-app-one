<?php

declare(strict_types=1);

namespace Outsourced\Pernambucanas\Connections\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

final class PernambucanasHeaders
{
    private const CONFIG = 'outsourced.'. NetworkEnum::PERNAMBUCANAS;

    public static function getUri(): ?string
    {
        return config(self::CONFIG . '.uri');
    }

    public static function getAuthorization(): ?string
    {
        return config(self::CONFIG . '.authorization');
    }
}
