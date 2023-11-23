<?php

namespace Reports\SubModules\Hourly\Helpers;

use Reports\SubModules\Hourly\Constants\PrePosLineActivationOperations;
use TradeAppOne\Domain\Enumerators\Operations;

class ScaffoldToGroupOfOperators
{
    public static function createPosPagoWithCustomOperation($operators, $custom)
    {
        return self::createWithCustomOperation($operators, $custom, PrePosLineActivationOperations::POS);
    }

    public static function createPrePagoWithCustomOperation($operators, $custom)
    {
        return self::createWithCustomOperation($operators, $custom, PrePosLineActivationOperations::PRE);
    }

    public static function createWithCustomOperation($operators, $custom, $intersectGroup)
    {
        $pos      = [];
        $services = $operators[Operations::LINE_ACTIVATION];

        foreach ($services as $operator => $operations) {
            $pos[$operator] = array_intersect($operations, $intersectGroup);
        }

        $mappedPos = [];

        foreach (array_filter($pos) as $operator => $operations) {
            $mappedPos[$operator] = $custom;
        }

        return $mappedPos;
    }
}
