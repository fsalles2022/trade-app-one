<?php


namespace Outsourced\Cea\ConsultaSerialConnection;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class CeaSerialHeaders
{
    public const CONFIG = 'outsourced.'. NetworkEnum::CEA;

    public static function uri(): string
    {
        return config(self::CONFIG . '.uri_triangulation');
    }
}
