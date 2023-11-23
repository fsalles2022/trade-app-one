<?php


namespace Generali\Connection\Headers;

use TradeAppOne\Domain\Enumerators\NetworkEnum;

class GeneraliHeaders
{
    private const CONFIG = 'integrations.'. NetworkEnum::GENERALI;

    public static function getUri(): string
    {
        return config(self::CONFIG . '.uri');
    }

    public static function getMail(): string
    {
        return config(self::CONFIG . '.email');
    }

    public static function getPassword(): string
    {
        return config(self::CONFIG . '.senha');
    }
}
