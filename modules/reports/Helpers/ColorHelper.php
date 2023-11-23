<?php

namespace Reports\Helpers;

class ColorHelper
{
    const MAX_VALUE = 255;

    public static function getBrightenColor(array $color, float $percentage = 0.1)
    {
        $i                    = intval(self::MAX_VALUE * $percentage);
        [$red, $green, $blue] = $color;
        $maxValue             = self::MAX_VALUE - $i;
        if ($green < $maxValue && $blue < $maxValue) {
            $green += $i;
            $blue  += $i;
        }
        return self::colorToRGB([$red, $green, $blue]);
    }

    public static function colorToRGB(array $color)
    {
        $strColor = implode(',', $color);
        return "rgb($strColor)";
    }
}
