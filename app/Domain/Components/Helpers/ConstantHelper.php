<?php

namespace TradeAppOne\Domain\Components\Helpers;

use ReflectionClass;

class ConstantHelper
{
    public static function getAllConstants($ConstantClass): array
    {
        $calledClass = new ReflectionClass($ConstantClass);
        return $calledClass->getConstants();
    }

    public static function getValue($constantClass, $key)
    {
        $constants = ConstantHelper::getAllConstants($constantClass);
        return $constants[$key] ?? null;
    }

    public static function getGroupOfValues($constantClass, $group)
    {
        if (is_array($group)) {
            $constants = ConstantHelper::getAllConstants($constantClass);
            return array_intersect($group, $constants);
        }

        return null;
    }
}
