<?php


namespace Outsourced\Cea\GiftCardConnection;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class CeaHeaders
{
    public const CONFIG = 'outsourced.'. NetworkEnum::CEA;

    public static function uri(): string
    {
        return config(self::CONFIG . '.uri', '');
    }

    public static function login(): string
    {
        return config(self::CONFIG . '.login');
    }

    public static function password(): string
    {
        return config(self::CONFIG . '.password');
    }
}
