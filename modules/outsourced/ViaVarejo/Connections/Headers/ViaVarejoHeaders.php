<?php


namespace Outsourced\ViaVarejo\Connections\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class ViaVarejoHeaders
{
    private const CONFIG = 'outsourced.'. NetworkEnum::VIA_VAREJO;

    public function getUri(): string
    {
        return config(self::CONFIG . '.uri');
    }
}
