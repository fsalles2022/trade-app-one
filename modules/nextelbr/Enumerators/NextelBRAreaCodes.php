<?php

namespace NextelBR\Enumerators;

final class NextelBRAreaCodes
{
    const AREA_CODES = ["11", "12", "13", "19", "21", "22", "24"];

    public static function areaCodesString(): string
    {
        return implode(',', self::AREA_CODES);
    }
}
