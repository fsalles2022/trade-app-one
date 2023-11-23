<?php


namespace Outsourced\GPA\Connections\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class GPAHeaders
{
    private const CONFIG = 'outsourced.'. NetworkEnum::GPA;

    public static function getUri(): string
    {
        return config(self::CONFIG . '.uri');
    }

    public static function username(): string
    {
        return config(self::CONFIG . '.username');
    }

    public static function password(): string
    {
        return config(self::CONFIG . '.password');
    }

    public static function grantType(): string
    {
        return config(self::CONFIG . '.grant_type');
    }

    public static function xApiKey(): string
    {
        return config(self::CONFIG. '.x_api_key');
    }
}
