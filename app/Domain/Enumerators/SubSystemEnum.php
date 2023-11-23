<?php

namespace TradeAppOne\Domain\Enumerators;

final class SubSystemEnum
{
    public const API               = 'API';
    public const WEB               = 'WEB';
    public const APP               = 'APP';
    public const SIV               = 'SIV';
    public const INTERNET          = 'INTERNET';
    public const SUPPORTED_CLIENTS = [self::APP, self::WEB, self::API, self::INTERNET, self::SIV];
}
